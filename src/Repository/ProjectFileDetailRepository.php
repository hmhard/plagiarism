<?php

namespace App\Repository;

use App\Entity\ProjectFileDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectFileDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectFileDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectFileDetail[]    findAll()
 * @method ProjectFileDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectFileDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectFileDetail::class);
    }

    // /**
    //  * @return ProjectFileDetail[] Returns an array of ProjectFileDetail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProjectFileDetail
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
