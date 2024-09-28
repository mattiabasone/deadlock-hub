<?php

declare(strict_types=1);

namespace DeadlockHub\GameNews\PlayDeadlock\Rss;

use DeadlockHub\GameNews\Steam\GetNewsForAppResponse\NewsItem;
use DeadlockHub\Infrastructure\Serializer\ArrayCollectionNormalizer;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

readonly class Channel
{
    public function __construct(
        public string $title,
        public string $description,
        #[Context([DateTimeNormalizer::FORMAT_KEY => 'D, d M Y H:i:s O'])]
        public string $pubDate,
        #[Context([DateTimeNormalizer::FORMAT_KEY => 'D, d M Y H:i:s O'])]
        public \DateTimeImmutable $lastBuildDate,
        #[Context([ArrayCollectionNormalizer::INNER_TYPE => NewsItem::class])]
        public ArrayCollection $items,
    ) {
    }
}
