<?php

declare(strict_types=1);

namespace DeadlockHub\Repository\Telegram;

use DeadlockHub\Entity\Telegram\NewsSubscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NewsSubscription>
 *
 * @method null|NewsSubscription find($id, $lockMode = null, $lockVersion = null)
 * @method null|NewsSubscription findOneBy(array $criteria, array $orderBy = null)
 * @method NewsSubscription[]    findAll()
 * @method NewsSubscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsSubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsSubscription::class);
    }

    public function create(string $subscriberId): NewsSubscription
    {
        $new = new NewsSubscription($subscriberId);
        $this->getEntityManager()->persist($new);
        $this->getEntityManager()->flush();

        return $new;
    }

    public function delete(NewsSubscription $newsSubscription): void
    {
        $this->getEntityManager()->remove($newsSubscription);
        $this->getEntityManager()->flush();
    }
}
