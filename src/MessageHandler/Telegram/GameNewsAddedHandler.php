<?php

declare(strict_types=1);

namespace DeadlockHub\MessageHandler\Telegram;

use DeadlockHub\Message\Telegram\GameNewsAdded;
use DeadlockHub\Message\Telegram\NotifySubscriber;
use DeadlockHub\Repository\GameNewsRepository;
use DeadlockHub\Repository\Telegram\NewsSubscriptionRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final readonly class GameNewsAddedHandler
{
    public function __construct(
        private GameNewsRepository $gameNewsRepository,
        private NewsSubscriptionRepository $newsSubscriptionRepository,
        private MessageBusInterface $messageBus
    ) {
    }

    public function __invoke(GameNewsAdded $message)
    {
        $gameNews = $this->gameNewsRepository->findByTypeAndIdentifier($message->type, $message->identifier);
        foreach ($this->newsSubscriptionRepository->findAll() as $subscriber) {
            $this->messageBus->dispatch(
                new NotifySubscriber(
                    $subscriber->getSubscriberId(),
                    $gameNews->getType(),
                    $gameNews->getMessage()
                )
            );
        }
    }
}
