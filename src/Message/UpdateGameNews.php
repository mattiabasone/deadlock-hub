<?php

declare(strict_types=1);

namespace DeadlockHub\Message;

use DeadlockHub\Entity\Enum\GameNewsType;

final readonly class UpdateGameNews
{
    public function __construct(
        public GameNewsType $gameNewsType,
    ) {
    }
}
