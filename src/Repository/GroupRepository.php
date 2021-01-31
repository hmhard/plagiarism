<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    private $security;
    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Group::class);
        $this->security=$security;
    }

    // /**
    //  * @return Group[] Returns an array of Group objects
    //  */
  
    public function getData($value=[])
    {
        return $this->createQueryBuilder('g')
            // ->andWhere('g.exampleField = :val')
            // ->setParameter('val', $value)
            ->orderBy('g.id', 'DESC')
         
            ->getQuery()
         
        ;
    }

    public function getMyGroups($filter = [])
    {

        $qb = $this->createQueryBuilder('g');
        if (isset($filter['user'])) {
            $qb = $qb->join("App:GroupMember", "gm","WITH", "gm.belongsTo = g.id")
                ->andWhere('gm.user in (:member)')
                ->setParameter('member', $filter['user']);
        }
        if (isset($filter['user_me'])) {

          
            if($this->security->getUser()->getUserType()->getId()==1){
                $qb = $qb->join("App:GroupMember", "gm","WITH", "gm.belongsTo = g.id")
                ->andWhere('gm.user in (:member)')
                ->setParameter('member', $this->security->getUser());
            }

                return $qb->orderBy('g.id', 'ASC');

           
        }
       

        return $qb->orderBy('g.id', 'ASC')

            ->getQuery()
            ->getResult();
    }
   

    /*
    public function findOneBySomeField($value): ?Group
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
