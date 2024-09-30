<?php

declare(strict_types=1);

namespace DeadlockHub\GameNews;

use DeadlockHub\Entity\Enum\GameNewsType;
use DeadlockHub\Message\Telegram\GameNewsAdded;
use DeadlockHub\Repository\GameNewsRepository;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class NewsHandler
{
    // Two hours
    private const string TOO_OLD_DISCARD_INTERVAL = 'PT2H';

    public function __construct(
        private GameNewsRepository $gameNewsRepository,
        private MessageBusInterface $messageBus,
        private ClockInterface $clock,
        private LoggerInterface $logger,
    ) {
    }

    public function recordNews(GameNewsType $type, string $identifier, string $message): void
    {
        $this->gameNewsRepository->store($identifier, $type, $message);

        $this->messageBus->dispatch(new GameNewsAdded($identifier, $type));
    }

    public function shouldStreamNews(GameNewsType $type, string $identifier, \DateTimeImmutable $publishedAt): bool
    {
        $logContext = [
            'identifier' => $identifier,
            'published_at' => $publishedAt->format(\DateTimeInterface::ATOM),
            'type' => $type->value,
        ];

        if (!\is_null($this->gameNewsRepository->findByTypeAndIdentifier($type, $identifier))) {
            $this->logger->debug("Entry found, skipping", $logContext);

            return false;
        }

        if ($this->isTooOld($publishedAt)) {
            $this->logger->debug("Feed entry too old", $logContext);

            return false;
        }

        return true;
    }

    /**
     * The news is considered too old if it was published more than two hours ago.
     */
    private function isTooOld(\DateTimeImmutable $publishedAt): bool
    {
        $twoHoursAgo = $this->clock->now()->sub(new \DateInterval(self::TOO_OLD_DISCARD_INTERVAL));

        if ($publishedAt < $twoHoursAgo) {
            return true;
        }

        return false;
    }
}
