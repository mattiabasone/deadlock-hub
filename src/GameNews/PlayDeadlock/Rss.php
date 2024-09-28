<?php

declare(strict_types=1);

namespace DeadlockHub\GameNews\PlayDeadlock;

use DeadlockHub\GameNews\PlayDeadlock\Rss\Channel;

readonly class Rss
{
    public function __construct(
        public Channel $channel
    ) {
    }
}
