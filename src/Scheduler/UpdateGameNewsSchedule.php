<?php

declare(strict_types=1);

namespace DeadlockHub\Scheduler;

use DeadlockHub\Entity\Enum\GameNewsType;
use DeadlockHub\Message\UpdateGameNews;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsSchedule]
final class UpdateGameNewsSchedule implements ScheduleProviderInterface
{
    public function __construct(
        private CacheInterface $cache,
    ) {
    }

    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                RecurringMessage::every('180 seconds', new UpdateGameNews(GameNewsType::SteamNews)),
            )
            ->stateful($this->cache)
        ;
    }
}
