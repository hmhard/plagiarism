<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    // /**
    //  * @return Project[] Returns an array of Project objects
    //  */
   
    public function getData($value=[])
    {
        return $this->createQueryBuilder('p')
            // ->andWhere('p.exampleField = :val')
            // ->setParameter('val', $value)
            ->orderBy('p.id', 'DESC')
         
            ->getQuery()
           
        ;
    }
    public function getCount($filter=[])
    {
        $qb= $this->createQueryBuilder('p')
        ->select('count(p)');
        if(isset($filter['status']) && sizeof($filter['status'])>0)
            $qb=$qb->andWhere('p.status in  (:status)')
            ->setParameter('status', $filter['status']);


        return $qb->orderBy('p.id', 'DESC')
         
            ->getQuery()
            ->getSingleScalarResult()
           
        ;
    }


    /*
    public function findOneBySomeField($value): ?Project
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
