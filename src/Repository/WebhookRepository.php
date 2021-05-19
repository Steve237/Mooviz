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

    //affiche le total de webhooks enregistrés en bdd
    public function countWebhooks() {

        return $this->createQueryBuilder('w')
            ->select('count(w.id)')
            ->getQuery()
            ->getSingleScalarResult();  
        
    }





}
