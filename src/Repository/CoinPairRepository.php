<?php

namespace App\Repository;

use App\Entity\CoinPair;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CoinPair|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoinPair|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoinPair[]    findAll()
 * @method CoinPair[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoinPairRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoinPair::class);
    }

    // /**
    //  * @return CoinPair[] Returns an array of CoinPair objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CoinPair
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
