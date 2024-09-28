<?php

declare(strict_types=1);

namespace DeadlockHub\Message\Telegram;

use DeadlockHub\Entity\Enum\GameNewsType;

final readonly class GameNewsAdded
{
    public function __construct(
        public string $identifier,
        public GameNewsType $type
    ) {
    }
}
