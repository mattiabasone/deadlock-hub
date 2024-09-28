<?php

declare(strict_types=1);

namespace DeadlockHub\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DeadlockHub\Entity\PlanetOwnershipChange;

/**
 * @extends ServiceEntityRepository<PlanetOwnershipChange>
 *
 * @method null|PlanetOwnershipChange find($id, $lockMode = null, $lockVersion = null)
 * @method null|PlanetOwnershipChange findOneBy(array $criteria, array $orderBy = null)
 * @method PlanetOwnershipChange[]    findAll()
 * @method PlanetOwnershipChange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanetOwnershipChangesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanetOwnershipChange::class);
    }

    //    /**
    //     * @return PlanetOwnerships[] Returns an array of PlanetOwnerships objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PlanetOwnerships
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
