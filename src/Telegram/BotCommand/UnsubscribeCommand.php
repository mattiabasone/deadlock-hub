<?php

declare(strict_types=1);

namespace DeadlockHub\Telegram\BotCommand;

use DeadlockHub\Repository\Telegram\NewsSubscriptionRepository;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class UnsubscribeCommand extends Command
{
    protected string $name = 'unsubscribe';
    protected string $description = 'Unsubscribe for game updates';

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

        if (\is_null($newsSubscriber)) {
            $this->replyWithMessage([
                'text' => 'You aren\'t subscribed for game updates.',
            ]);

            return;
        }

        $this->newsSubscriptionRepository->delete($newsSubscriber);

        $this->replyWithMessage([
            'text' => 'This channel has been unregistered for game updates.',
        ]);
    }
}
