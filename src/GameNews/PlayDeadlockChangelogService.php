<?php

declare(strict_types=1);

namespace DeadlockHub\GameNews;

use DeadlockHub\Entity\Enum\GameNewsType;
use DeadlockHub\GameNews\PlayDeadlock\ChangelogFeedEntry;
use DeadlockHub\GameNews\PlayDeadlock\RssFetcher;
use DeadlockHub\Message\Telegram\GameNewsAdded;
use DeadlockHub\Repository\GameNewsRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class PlayDeadlockChangelogService implements NewsServiceInterface
{
    public function __construct(
        private readonly RssFetcher $rssFetcher,
        private readonly GameNewsRepository $gameNewsRepository,
        private readonly ClockInterface $clock,
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger
    ) {
    }

    public function processNews(): void
    {
        $changelogEntries = $this->rssFetcher->fetchChangelogFeed();

        $streamableNews = $changelogEntries
            ->filter(fn (ChangelogFeedEntry $newsItem): bool => $this->shouldStreamNews($newsItem));

        foreach ($streamableNews as $newsItem) {
            $identifier = $newsItem->gid;
            $message = <<<MESSAGE
                📰 <b>{$newsItem->title}</b>
                
                {$newsItem->description}
                
                {$newsItem->url}
                MESSAGE;

            $this->gameNewsRepository->store($identifier, GameNewsType::PlayDeadlockChangelogNews, $message);

            $this->messageBus->dispatch(new GameNewsAdded($identifier, GameNewsType::PlayDeadlockChangelogNews));
        }
    }

    private function shouldStreamNews(ChangelogFeedEntry $feedEntry): bool
    {
        $logContext = [
            'gid' => $feedEntry->id,
            'published' => $feedEntry->publishedAt->format(\DateTimeInterface::ATOM),
        ];

        if (!\is_null($this->gameNewsRepository->findByTypeAndIdentifier(GameNewsType::PlayDeadlockChangelogNews, $feedEntry->id))) {
            $this->logger->debug("Steam news found, skipping", $logContext);

            return false;
        }

        $newsAge = $this->clock->now()->diff($feedEntry->publishedAt);
        if ($newsAge->d !== 0 || $newsAge->h > 2) {
            $this->logger->debug("Steam news too old", $logContext);

            return false;
        }

        return true;
    }
}
