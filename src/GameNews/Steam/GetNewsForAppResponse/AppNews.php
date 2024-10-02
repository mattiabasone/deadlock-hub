<?php

declare(strict_types=1);

namespace DeadlockHub\GameNews\Steam\GetNewsForAppResponse;

use DeadlockHub\Infrastructure\Serializer\LoophpCollectionNormalizer;
use loophp\collection\Collection;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\SerializedName;

/**
 * @property Collection<int, NewsItem> $newsItems
 */
readonly class AppNews
{
    public function __construct(
        #[SerializedName('appid')]
        public int $appId,
        #[SerializedName('newsitems')]
        #[Context([LoophpCollectionNormalizer::INNER_TYPE => NewsItem::class])]
        public Collection $newsItems,
        public int $count
    ) {
    }
}
