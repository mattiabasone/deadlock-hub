<?php

declare(strict_types=1);

namespace DeadlockHub\GameNews\PlayDeadlock\Rss;

use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

readonly class Item
{
    public function __construct(
        private string $title,
        private string $link,
        private string $description,
        #[Context([DateTimeNormalizer::FORMAT_KEY => 'D, d M Y H:i:s O'])]
        private string $pubDate,
        #[SerializedName('dc:creator')]
        private string $creator,
        #[SerializedName('content:encoded')]
        private string $content,
        private array $categories = [],
    ) {
    }
}
