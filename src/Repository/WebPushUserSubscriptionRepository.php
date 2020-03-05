<?php

namespace App\Repository;

use App\Entity\WebPushUserSubscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method WebPushUserSubscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method WebPushUserSubscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method WebPushUserSubscription[]    findAll()
 * @method WebPushUserSubscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WebPushUserSubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebPushUserSubscription::class);
    }

    // /**
    //  * @return WebPushUserSubscription[] Returns an array of WebPushUserSubscription objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WebPushUserSubscription
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
