<?php

namespace App\Repository;

use App\Entity\ScheduleItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ScheduleItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScheduleItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScheduleItem[]    findAll()
 * @method ScheduleItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduleItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScheduleItem::class);
    }

    // /**
    //  * @return ScheduleItem[] Returns an array of ScheduleItem objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ScheduleItem
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
