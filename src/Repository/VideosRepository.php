<?php

namespace App\Repository;

use App\Entity\Videos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Videos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Videos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Videos[]    findAll()
 * @method Videos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Videos::class);
    }

    public function getVideoByCategory($category) {

        return $this->createQueryBuilder('v')
        ->andwhere('v.category = :val')
        ->setParameter('val', $category)
        ->getQuery()
        ->getResult()
        
        ;
    }

    public function showVideoByCategory($category, $id) {

        return $this->createQueryBuilder('v')
        ->andwhere('v.category = :val')
        ->andwhere('v.id != :id')
        ->setParameter('val', $category)
        ->setParameter('id', $id)
        ->getQuery()
        ->getResult()
        
        ;
    }

    public function search($videoTitle) {
        return $this->createQueryBuilder('v')
            ->andWhere('v.videotitle LIKE :videotitle')
            ->setParameter('videotitle', '%'.$videoTitle.'%')
            ->getQuery()
            ->execute();
    }


    
    
    
    /*
    public function findOneBySomeField($value): ?Videos
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
