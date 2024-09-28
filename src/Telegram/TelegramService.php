<?php

declare(strict_types=1);

namespace DeadlockHub\Telegram;

use DeadlockHub\Telegram\BotCommand\StartCommand;
use DeadlockHub\Telegram\BotCommand\SubscribeCommand;
use DeadlockHub\Telegram\BotCommand\UnsubscribeCommand;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

readonly class TelegramService
{
    public function __construct(
        private Api $telegramApi,
        private StartCommand $startCommand,
        private SubscribeCommand $subscribeCommand,
        private UnsubscribeCommand $unsubscribeCommand,
    ) {
        $this->telegramApi->addCommands([
            $this->startCommand,
            $this->subscribeCommand,
            $this->unsubscribeCommand,
        ]);
    }

    public function handleWebhookUpdate(Update $update): void
    {
        $this->telegramApi->processCommand($update);
    }
}
