<?php

declare(strict_types=1);

namespace DeadlockHub\GameNews;

use Doctrine\ORM\EntityManagerInterface;
use DeadlockHub\Entity\Enum\GameNewsType;
use DeadlockHub\Entity\GameNews;
use DeadlockHub\Message\Telegram\GameNewsAdded;
use DeadlockHub\Repository\GameNewsRepository;
use DeadlockHub\Steam\GetNewsForAppResponse\NewsItem;
use DeadlockHub\Steam\SteamNewsApi;
use Psr\Log\LoggerInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class SteamNewsService
{
    public function __construct(
        private SteamNewsApi $steamNewsApi,
        private ClockInterface $clock,
        private EntityManagerInterface $entityManager,
        private GameNewsRepository $gameNewsRepository,
        private LoggerInterface $logger,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function processNews(): void
    {
        $news = $this->steamNewsApi->fetch();

        $streamableNews = $news->appNews->newsItems
            ->filter(fn (NewsItem $newsItem): bool => $this->shouldStreamNews($newsItem));
        foreach ($streamableNews as $newsItem) {

            $identifier = $newsItem->gid;
            $message = <<<MESSAGE
                📰 <b>{$newsItem->title}</b>
                
                {$newsItem->url}
                MESSAGE;

            $this->entityManager->persist(
                (new GameNews())
                    ->setIdentifier($identifier)
                    ->setType(GameNewsType::SteamNews)
                    ->setMessage($message)
            );
            $this->entityManager->flush();

            $this->messageBus->dispatch(new GameNewsAdded($identifier, GameNewsType::SteamNews));
        }
    }

    private function shouldStreamNews(NewsItem $newsItem): bool
    {
        $logContext = [
            'gid' => $newsItem->gid,
            'published' => $newsItem->getDateTime()->format(\DateTimeInterface::ATOM),
        ];

        if (!\is_null($this->gameNewsRepository->findByTypeAndIdentifier(GameNewsType::SteamNews, $newsItem->gid))) {
            $this->logger->debug("Steam news found, skipping", $logContext);

            return false;
        }

        $newsAge = $this->clock->now()->diff($newsItem->getDateTime());
        if ($newsAge->d !== 0 || $newsAge->h > 2) {
            $this->logger->debug("Steam news too old", $logContext);

            return false;
        }

        return true;
    }
}
