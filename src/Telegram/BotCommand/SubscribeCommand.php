<?php

declare(strict_types=1);

namespace DeadlockHub\Telegram\BotCommand;

use DeadlockHub\Repository\Telegram\NewsSubscriptionRepository;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class SubscribeCommand extends Command
{
    protected string $name = 'subscribe';
    protected string $description = 'Subscribe for game updates';

    public function __construct(
        private readonly NewsSubscriptionRepository $newsSubscriptionRepository
    ) {
    }

    public function handle(): void
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $newsSubscriber = $this->newsSubscriptionRepository->findOneBy(
            ['subscriberId' => (string) $this->getUpdate()->message->chat->id]
        );

        if (!\is_null($newsSubscriber)) {
            $this->replyWithMessage([
                'text' => 'You are already subscribed for game updates.',
            ]);

            return;
        }

        $this->newsSubscriptionRepository->create((string) $this->getUpdate()->message->chat->id);

        $this->replyWithMessage([
            'text' => 'This chat has been registered for game updates!',
        ]);
    }
}
