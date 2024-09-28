<?php

declare(strict_types=1);

namespace DeadlockHub\Entity;

use DeadlockHub\Entity\Enum\GameNewsType;
use DeadlockHub\Repository\GameNewsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table('game_news')]
#[ORM\Entity(repositoryClass: GameNewsRepository::class)]
#[ORM\UniqueConstraint('game_news_type_identifier_unique', columns: ['type', 'identifier'])]
#[ORM\Index('game_news_updated_at', columns: ['updated_at'])]
#[ORM\HasLifecycleCallbacks]
class GameNews
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'type', type: Types::STRING, nullable: false, enumType: GameNewsType::class)]
    private GameNewsType $type;

    #[ORM\Column(name: 'identifier', type: Types::STRING, nullable: false)]
    private string $identifier;

    #[ORM\Column(name: 'message', type: Types::TEXT, nullable: false)]
    private string $message;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setType(GameNewsType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): GameNewsType
    {
        return $this->type;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public static function planetOwnershipChangeIdentifier(
        int $planetIndex,
        int $previousOwner,
        int $newOwner,
        \DateTimeInterface $dateTimeChange
    ): string {
        return \sprintf(
            '%d-%d-%d-%s',
            $planetIndex,
            $previousOwner,
            $newOwner,
            $dateTimeChange->format('YmdH')
        );
    }
}
