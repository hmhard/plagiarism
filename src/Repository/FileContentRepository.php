<?php

namespace App\Repository;

use App\Entity\FileContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FileContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileContent[]    findAll()
 * @method FileContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileContent::class);
    }

    // /**
    //  * @return FileContent[] Returns an array of FileContent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FileContent
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
