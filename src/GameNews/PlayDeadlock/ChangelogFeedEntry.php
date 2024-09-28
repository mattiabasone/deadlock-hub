<?php

declare(strict_types=1);

namespace DeadlockHub\GameNews\PlayDeadlock;

readonly class ChangelogFeedEntry
{
    public function __construct(
        public string $id,
        public string $title,
        public string $content,
        public string $link,
        public ?\DateTimeImmutable $publishedAt,
    ) {
    }
}
