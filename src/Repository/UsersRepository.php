<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\ORM\Query;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Collection;

/**
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }
  
    // permet de récupérer un user
    public function findUser($user)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :val')
            ->setParameter('val', $user)
            ->getQuery()
            ->getResult()
        ;
    }

    
    public function findEmail($email)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->setParameter('val', $email)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getCustomer($customer)
    {
        return $this->createQueryBuilder('u')
            ->select('u.username')
            ->andWhere('u.customerid = :val')
            ->setParameter('val', $customer)
            ->getQuery()
            ->getSingleResult()
        ;
    }


    public function findCustomer($customer)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.customerid = :val')
            ->setParameter('val', $customer)
            ->getQuery()
            ->getSingleResult()
        ;
    }

    
    public function verifSubscriber($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.verifsubscription = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }

  
     // assure la recherche des utilisateurs par nom
     public function userSearch($username) {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username LIKE :username')
            ->setParameter('username', '%'.$username.'%')
            ->getQuery()
            ->execute();
    }


    //affiche le total de users inscrits
    public function countUsers() {

        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();  
        
    }

    /*
    public function findOneBySomeField($value): ?Users
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
