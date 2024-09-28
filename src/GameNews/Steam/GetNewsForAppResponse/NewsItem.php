<?php

declare(strict_types=1);

namespace DeadlockHub\GameNews\Steam\GetNewsForAppResponse;

use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class NewsItem
{
    public function __construct(
        public string $gid,
        public string $title,
        public string $url,
        #[SerializedName('is_external_url')]
        public bool $isExternalUrl,
        public string $author,
        public string $contents,
        #[SerializedName('feedlabel')]
        public string $feedLabel,
        #[SerializedName('feedname')]
        public string $feedName,
        #[SerializedName('feed_type')]
        public int $feedType,
        public int $date,
        public array $tags = [],
    ) {
    }

    public function getDateTime(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat('U', (string) $this->date);
    }
}
