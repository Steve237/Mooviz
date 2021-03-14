<?php

namespace App\Repository;

use App\Entity\Webhook;
use Doctrine\ORM\Query;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Webhook|null find($id, $lockMode = null, $lockVersion = null)
 * @method Webhook|null findOneBy(array $criteria, array $orderBy = null)
 * @method Webhook[]    findAll()
 * @method Webhook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WebhookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Webhook::class);
    }


     //permet de supprimer toutes les notifications
     public function deleteAllWebhook() {

        return $this->createQueryBuilder('w')
        ->delete()
        ->where('w.id != 0')
        ->getQuery()
        ->getResult();

    }

     //permet de réaliser la pagination
     public function findAllWithPagination() : Query
     {

        return $this->createQueryBuilder('w')
            ->getQuery();
        
    }


    // /**
    //  * @return Webhook[] Returns an array of Webhook objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Webhook
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
