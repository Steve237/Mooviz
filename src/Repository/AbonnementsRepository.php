<?php

namespace App\Repository;

use App\Entity\Abonnements;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Abonnements|null find($id, $lockMode = null, $lockVersion = null)
 * @method Abonnements|null findOneBy(array $criteria, array $orderBy = null)
 * @method Abonnements[]    findAll()
 * @method Abonnements[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbonnementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Abonnements::class);
    }

  
    public function findAllFollow($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.abonne = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }
   

    /*
    public function findOneBySomeField($value): ?Abonnements
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
