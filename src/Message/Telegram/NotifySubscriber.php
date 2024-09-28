<?php

declare(strict_types=1);

namespace DeadlockHub\Message\Telegram;

use DeadlockHub\Entity\Enum\GameNewsType;

final readonly class NotifySubscriber
{
    public function __construct(
        public string $subscriberId,
        public GameNewsType $gameNewsType,
        public string $messageText,
    ) {
    }
}
