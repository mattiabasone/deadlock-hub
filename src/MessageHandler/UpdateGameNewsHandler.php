<?php

declare(strict_types=1);

namespace DeadlockHub\MessageHandler;

use DeadlockHub\Entity\Enum\GameNewsType;
use DeadlockHub\GameNews\PlayDeadlockChangelogService;
use DeadlockHub\GameNews\SteamNewsService;
use DeadlockHub\Message\UpdateGameNews;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateGameNewsHandler
{
    public function __construct(
        private SteamNewsService $steamNewsService,
        private PlayDeadlockChangelogService $playDeadlockNewsService,
        private LoggerInterface $logger
    ) {

    }

    public function __invoke(UpdateGameNews $message): void
    {
        $this->logger->notice("Processing {$message->gameNewsType->value}");
        match ($message->gameNewsType) {
            GameNewsType::PlayDeadlockChangelogNews => $this->playDeadlockNewsService->processNews(),
            GameNewsType::SteamNews => $this->steamNewsService->processNews(),
        };
    }
}
