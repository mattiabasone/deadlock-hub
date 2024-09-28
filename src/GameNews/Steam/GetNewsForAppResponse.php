<?php

declare(strict_types=1);

namespace DeadlockHub\GameNews\Steam;

use DeadlockHub\GameNews\Steam\GetNewsForAppResponse\AppNews;
use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class GetNewsForAppResponse
{
    public function __construct(
        #[SerializedName('appnews')]
        public AppNews $appNews,
    ) {
    }
}
