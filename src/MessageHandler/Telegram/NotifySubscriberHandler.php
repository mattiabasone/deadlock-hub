<?php

declare(strict_types=1);

namespace DeadlockHub\MessageHandler\Telegram;

use DeadlockHub\Message\Telegram\NotifySubscriber;
use DeadlockHub\Telegram\Formatter\GameNewsFormatter;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Telegram\Bot\Api;

#[AsMessageHandler]
final readonly class NotifySubscriberHandler
{
    public function __construct(
        private Api $telegramApi,
        private GameNewsFormatter $gameNewsFormatter,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(NotifySubscriber $message): void
    {
        if ($message->messageText === 'void msg' || $message->messageText === '') {
            return;
        }

        $formattedMessage = $this->gameNewsFormatter->format($message->gameNewsType, $message->messageText);

        $this->telegramApi->sendMessage([
            'chat_id' => $message->subscriberId,
            'text' => $formattedMessage,
            'parse_mode' => 'HTML',
        ]);

        $this->logger->notice("Message sent to {$message->subscriberId}");
    }
}
