<?php

namespace App\Repository;

use App\Entity\Videos;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Collection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
    
    //affiche vidéos par categories
    public function getVideoByCategory($category) {

        return $this->createQueryBuilder('v')
        ->andwhere('v.category = :val')
        ->setParameter('val', $category)
        ->getQuery()
        ->getResult();
    }

    //Affiche 21 vidéos à la page de profil (nouveautés)
    public function getVideos() {

        return $this->createQueryBuilder('v')
        ->setMaxResults(21)
        ->orderBy('v.id', 'DESC')
        ->getQuery()
        ->getResult();
    }

      public function showVideo($id) {

        return $this->createQueryBuilder('v')
        ->andwhere('v.id = :id')
        ->setParameter('id', $id)
        ->orderBy('v.id', 'DESC')
        ->getQuery()
        ->getResult();
    }

    //affiche suggestion des vidéos de la même categorie que la vidéo choisi
    public function showVideoByCategory($category, $id) {

        return $this->createQueryBuilder('v')
        ->andwhere('v.category = :val')
        ->andwhere('v.id != :id')
        ->setParameter('val', $category)
        ->setParameter('id', $id)
        ->setMaxResults(15)
        ->getQuery()
        ->getResult()
        
        ;
    }
    
    // assure la recherche des vidéos par titre
    public function search($videoTitle) {
        return $this->createQueryBuilder('v')
            ->andWhere('v.videotitle LIKE :videotitle')
            ->setParameter('videotitle', '%'.$videoTitle.'%')
            ->getQuery()
            ->execute();
    }

    //permet de réaliser la pagination
    public function findAllWithPagination() : Query{

        return $this->createQueryBuilder('v')
            ->getQuery();
        
    }



    //affiche les vidéos de l'user
    public function getVideoByUser($user) {

        return $this->createQueryBuilder('v')
        ->andwhere('v.username = :val')
        ->setParameter('val', $user)
        ->getQuery()
        ->getResult()
        
        ;
    }

    // récupère liste des vidéos où le créateur est suivi par l'utilisateur courant

    public function findAllByUsers(Collection $users) {

        $qb = $this->createQueryBuilder('v');
        
        return $qb->select('v')
            ->andwhere('v.username IN (:following)')
            ->setParameter('following', $users)
            ->orderBy('v.id', 'DESC')
            ->getQuery()
            ->getResult();

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
