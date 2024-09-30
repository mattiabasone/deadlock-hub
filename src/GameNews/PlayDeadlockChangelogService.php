<?php

declare(strict_types=1);

namespace DeadlockHub\GameNews;

use DeadlockHub\Entity\Enum\GameNewsType;
use DeadlockHub\GameNews\PlayDeadlock\ChangelogFeedEntry;
use DeadlockHub\GameNews\PlayDeadlock\RssFetcher;

readonly class PlayDeadlockChangelogService implements NewsServiceInterface
{
    public function __construct(
        private RssFetcher $rssFetcher,
        private NewsHandler $newsHandler,
    ) {
    }

    public function processNews(): void
    {
        $changelogEntries = $this->rssFetcher->fetchChangelogFeed();

        $streamableNews = $changelogEntries
            ->filter(fn (ChangelogFeedEntry $feedEntry): bool => $this->newsHandler->shouldStreamNews(
                GameNewsType::PlayDeadlockChangelogNews,
                $feedEntry->id,
                $feedEntry->publishedAt
            ));

        foreach ($streamableNews as $feedEntry) {
            $identifier = $feedEntry->id;
            $message = <<<MESSAGE
                📰 <b>{$feedEntry->title}</b>
                
                {$feedEntry->content}
                
                {$feedEntry->link}
                MESSAGE;

            $this->newsHandler->recordNews(GameNewsType::PlayDeadlockChangelogNews, $identifier, $message);
        }
    }
}
