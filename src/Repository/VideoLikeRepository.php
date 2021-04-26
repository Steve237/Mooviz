<?php

namespace App\Repository;

use App\Entity\VideoLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VideoLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method VideoLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method VideoLike[]    findAll()
 * @method VideoLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoLike::class);
    }

    // /**
    //  * @return VideoLike[] Returns an array of VideoLike objects
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
    public function findOneBySomeField($value): ?VideoLike
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
