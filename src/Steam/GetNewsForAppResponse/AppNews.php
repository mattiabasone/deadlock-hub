<?php

declare(strict_types=1);

namespace DeadlockHub\Steam\GetNewsForAppResponse;

use Doctrine\Common\Collections\ArrayCollection;
use DeadlockHub\Infrastructure\Serializer\ArrayCollectionNormalizer;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\SerializedName;

/**
 * @property ArrayCollection<int, NewsItem> $newsItems
 */
readonly class AppNews
{
    public function __construct(
        #[SerializedName('appid')]
        public int $appId,
        #[SerializedName('newsitems')]
        #[Context([ArrayCollectionNormalizer::INNER_TYPE => NewsItem::class])]
        public ArrayCollection $newsItems,
        public int $count
    ) {
    }
}
