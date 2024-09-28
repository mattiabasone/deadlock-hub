<?php

declare(strict_types=1);

namespace DeadlockHub\GameNews;

use DeadlockHub\Entity\Enum\GameNewsType;
use DeadlockHub\GameNews\Steam\GetNewsForAppResponse\NewsItem;
use DeadlockHub\GameNews\Steam\SteamNewsApi;
use DeadlockHub\Message\Telegram\GameNewsAdded;
use DeadlockHub\Repository\GameNewsRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class SteamNewsService implements NewsServiceInterface
{
    public function __construct(
        private SteamNewsApi $steamNewsApi,
        private ClockInterface $clock,
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

            $this->gameNewsRepository->store($identifier, GameNewsType::SteamNews, $message);

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
