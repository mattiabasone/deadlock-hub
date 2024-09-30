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
        StartCommand $startCommand,
        SubscribeCommand $subscribeCommand,
        UnsubscribeCommand $unsubscribeCommand,
    ) {
        $this->telegramApi->addCommands([
            $startCommand,
            $subscribeCommand,
            $unsubscribeCommand,
        ]);
    }

    public function handleWebhookUpdate(Update $update): void
    {
        $this->telegramApi->processCommand($update);
    }
}
