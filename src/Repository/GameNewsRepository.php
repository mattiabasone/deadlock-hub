<?php

declare(strict_types=1);

namespace DeadlockHub\Repository;

use DeadlockHub\Entity\Enum\GameNewsType;
use DeadlockHub\Entity\GameNews;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameNews>
 *
 * @method null|GameNews find($id, $lockMode = null, $lockVersion = null)
 * @method null|GameNews findOneBy(array $criteria, array $orderBy = null)
 * @method GameNews[]    findAll()
 * @method GameNews[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameNewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameNews::class);
    }

    public function findByTypeAndIdentifier(GameNewsType $type, string $identifier): ?GameNews
    {
        return $this->createQueryBuilder('gs')
            ->where('gs.type = :type')
            ->andWhere('gs.identifier = :identifier')
            ->setParameter('type', $type->value)
            ->setParameter('identifier', $identifier)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function store(string $identifier, GameNewsType $type, string $message): void
    {
        $this->getEntityManager()->persist(new GameNews($identifier, $type, $message));
        $this->getEntityManager()->flush();
    }
}
