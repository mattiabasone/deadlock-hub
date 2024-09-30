<?php

declare(strict_types=1);

namespace DeadlockHub\GameNews\PlayDeadlock;

use Doctrine\Common\Collections\ArrayCollection;
use FeedIo\Feed\Item;
use FeedIo\FeedIo;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class RssFetcher
{
    private const string RSS_URL = "https://forums.playdeadlock.com/forums/changelog.10/index.rss";

    public function __construct(
        private readonly FeedIo $feedIo,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @throws \Throwable
     * @return ArrayCollection<int, ChangelogFeedEntry>
     */
    public function fetchChangelogFeed(): ArrayCollection
    {
        try {
            $result = $this->feedIo->read(self::RSS_URL);

            return new ArrayCollection(
                array_map(self::feedEntryToChangelogEntry(), iterator_to_array($result->getFeed()))
            );
        } catch (\Throwable $e) {
            $this->logger->error("Error fetching Changelog Rss: {$e->getMessage()}");

            throw $e;
        }
    }

    private static function feedEntryToChangelogEntry(): \Closure
    {
        return static function (Item $item): ChangelogFeedEntry {
            $title = $item->getTitle() ?? throw new \RuntimeException('Title is missing');
            $link = $item->getLink() ?? throw new \RuntimeException('Link is missing');
            $lastModified = $item->getLastModified() ?? throw new \RuntimeException('Last modified date is missing');

            return new ChangelogFeedEntry(
                id: $item->getPublicId() ?? Uuid::uuid4()->toString(),
                title: $title,
                content: $item->getValue('content:encoded') ?? '',
                link: $link,
                publishedAt: \DateTimeImmutable::createFromMutable($lastModified)
            );
        };
    }
}
