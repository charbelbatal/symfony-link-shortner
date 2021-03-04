<?php

namespace App\Repository;

use App\Entity\LinkStatistic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LinkStatistic|null find($id, $lockMode = null, $lockVersion = null)
 * @method LinkStatistic|null findOneBy(array $criteria, array $orderBy = null)
 * @method LinkStatistic[]    findAll()
 * @method LinkStatistic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkStatisticRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinkStatistic::class);
    }

    // /**
    //  * @return LinkStatistic[] Returns an array of LinkStatistic objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LinkStatistic
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
