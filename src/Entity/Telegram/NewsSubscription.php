<?php

declare(strict_types=1);

namespace DeadlockHub\Entity\Telegram;

use DeadlockHub\Entity\Timestampable;
use DeadlockHub\Repository\Telegram\NewsSubscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table('telegram_news_subscriptions')]
#[ORM\Entity(repositoryClass: NewsSubscriptionRepository::class)]
#[ORM\UniqueConstraint('telegram_news_subscriptions_subscriber_id_unique', columns: ['subscriber_id'])]
#[ORM\HasLifecycleCallbacks]
class NewsSubscription
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column('subscriber_id', type: Types::STRING, length: 64, options: ['unsigned' => false])]
    private string $subscriberId;

    public function __construct(string $subscriberId)
    {
        $this->subscriberId = $subscriberId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubscriberId(): string
    {
        return $this->subscriberId;
    }
}
