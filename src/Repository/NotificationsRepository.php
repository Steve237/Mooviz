<?php

namespace App\Repository;

use App\Entity\Notifications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notifications|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notifications|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notifications[]    findAll()
 * @method Notifications[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notifications::class);
    }


    //permet d'afficher les notifications
    public function findAllNotification($value)
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.destination', 'DESC')
            ->andWhere('n.destination = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }

    //permet d'afficher nombre de notifications
    public function numberNotif($user)
    {
        return $this->createQueryBuilder('n')
        ->select('count(n.id)')
        ->orderBy('n.id', 'DESC')
        ->andWhere('n.destination = :val')
        ->setParameter('val', $user)
        ->getQuery()
        ->getSingleScalarResult();
    }

    //permet de supprimer toutes les notifications
    public function deleteAllNotif($value) {

        return $this->createQueryBuilder('n')
        ->delete()
        ->where('n.destination = :val')
        ->setParameter('val', $value)
        ->getQuery()
        ->getResult();

    }

   
}
