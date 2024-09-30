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

    public function __construct(
        string $identifier,
        GameNewsType $type,
        string $message
    ) {
        $this->identifier = $identifier;
        $this->type = $type;
        $this->message = $message;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): GameNewsType
    {
        return $this->type;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
