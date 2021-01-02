<?php

namespace App\Repository;

use App\Entity\SimilarityHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SimilarityHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method SimilarityHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method SimilarityHistory[]    findAll()
 * @method SimilarityHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SimilarityHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SimilarityHistory::class);
    }

    // /**
    //  * @return SimilarityHistory[] Returns an array of SimilarityHistory objects
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
    public function findOneBySomeField($value): ?SimilarityHistory
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