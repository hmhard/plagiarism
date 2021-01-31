<?php

namespace App\Repository;

use App\Entity\ProjectSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectSession[]    findAll()
 * @method ProjectSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectSession::class);
    }

    // /**
    //  * @return ProjectSession[] Returns an array of ProjectSession objects
    //  */
  
   
    public function getActiveProject()
    {
        $now=(new \DateTime('now'))->format('Y-m-d');
        // dd($now);
        $qb= $this->createQueryBuilder('p')
        ->andWhere(" :now between  p.timeFrom and p.timeTo")
        ->setParameter('now', $now)
           
            ->orderBy('p.id', 'ASC')
            
            ->getQuery()
            ->getResult()
        ;
       return $qb;
    }
 

    /*
    public function findOneBySomeField($value): ?ProjectSession
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
