<?php

namespace App\Repository;

use App\Entity\Videodislike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Videodislike|null find($id, $lockMode = null, $lockVersion = null)
 * @method Videodislike|null findOneBy(array $criteria, array $orderBy = null)
 * @method Videodislike[]    findAll()
 * @method Videodislike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideodislikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Videodislike::class);
    }

    // /**
    //  * @return Videodislike[] Returns an array of Videodislike objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Videodislike
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
