<?php

namespace App\Repository;

use App\Entity\Videos;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Collection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use phpDocumentor\Reflection\PseudoTypes\False_;

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
    
    //affiche vidéos publiques par categorie
    public function getVideoByCategory($category) {

        return $this->createQueryBuilder('v')
        ->andwhere('v.category = :val')
        ->andwhere('v.privacy != :privacy')
        ->setParameter('val', $category)
        ->setParameter('privacy', 'private')
        ->orderBy('v.id', 'DESC')
        ->getQuery()
        ->getResult();
    }

    //affiche les vidéos publics sur la page accueil
    public function findAllPublicVideos() {

        return $this->createQueryBuilder('v')
        ->andwhere('v.privacy != :val')
        ->setParameter('val', 'private')
        ->orderBy('v.id', 'DESC')
        ->getQuery()
        ->getResult();
    }


    //affiche les nouvelles vidéos publics du site
    public function getVideos($video) {

        return $this->createQueryBuilder('v')
        ->andwhere('v.privacy != :val')
        ->andwhere('v.id != :video')
        ->setParameter('val', 'private')
        ->setParameter('video', $video)
        ->orderBy('v.id', 'DESC')
        ->setMaxResults(20)
        ->getQuery()
        ->getResult();
    }


        //recupère vidéo par id
      public function showVideo($id) {

        return $this->createQueryBuilder('v')
        ->andwhere('v.id = :id')
        ->setParameter('id', $id)
        ->orderBy('v.id', 'DESC')
        ->getQuery()
        ->getResult();
    }

      //recupère vidéo par id de celui qui l'a posté
      public function showVideoByUserId($userId, $video) {

        return $this->createQueryBuilder('v')
        ->andwhere('v.username = :userid')
        ->andwhere('v.privacy != :val')
        ->andwhere('v.id != :video')
        ->setParameter('userid', $userId)
        ->setParameter('val', 'private')
        ->setParameter('video', $video)
        ->orderBy('v.id', 'DESC')
        ->setMaxResults(20)
        ->getQuery()
        ->getResult();
    }


    //affiche suggestion des vidéos de la même categorie que la vidéo choisi
    public function showVideoByCategory($category, $id) {

        return $this->createQueryBuilder('v')
        ->andwhere('v.category = :val')
        ->andwhere('v.id != :id')
        ->andwhere('v.privacy != :privacy')
        ->setParameter('val', $category)
        ->setParameter('id', $id)
        ->setParameter('privacy', 'private')
        ->orderBy('v.id', 'DESC')
        ->setMaxResults(15)
        ->getQuery()
        ->getResult()
        
        ;
    }
    
    // assure la recherche des vidéos par titre
    public function search($videoTitle) {
        return $this->createQueryBuilder('v')
            ->andWhere('v.videotitle LIKE :videotitle')
            ->andwhere('v.privacy != :privacy')
            ->setParameter('videotitle', '%'.$videoTitle.'%')
            ->setParameter('privacy', 'private')
            ->getQuery()
            ->execute();
    }

    // assure la recherche des vidéos d'un user par titre
    public function userVideoSearch($videoTitle, $username) {
        return $this->createQueryBuilder('v')
            ->andWhere('v.videotitle LIKE :videotitle')
            ->andWhere('v.username = :val')
            ->setParameter('videotitle', '%'.$videoTitle.'%')
            ->setParameter('val', $username)
            ->getQuery()
            ->execute();
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
            ->andwhere('v.privacy != :privacy')
            ->setParameter('following', $users)
            ->setParameter('privacy', 'private')
            ->orderBy('v.id', 'DESC')
            ->getQuery()
            ->getResult();

    }

    //affiche le nombre total de vidéos postés par user 
    public function countUserVideos($user) {

        return $this->createQueryBuilder('v')
            ->select('count(v.username)')
            ->andWhere('v.username = :val')
            ->setParameter('val', $user)
            ->getQuery()
            ->getSingleScalarResult();  
        
    }

     //affiche le nombre total de vidéos postés 
     public function countVideos() {

        return $this->createQueryBuilder('v')
            ->select('count(v.id)')
            ->getQuery()
            ->getSingleScalarResult();  
        
    }


     //affiche le nombre total de vidéos postés par catégorie
     public function countVideosByCategory($category) {

        return $this->createQueryBuilder('v')
            ->select('count(v.id)')
            ->andWhere('v.category = :val')
            ->setParameter('val', $category)
            ->getQuery()
            ->getSingleScalarResult();  
        
    }



    //affiche le nombre total de membres qui vous suivent
    public function countFollower(Collection $users) {

        return $this->createQueryBuilder('v')
            ->select('count(DISTINCT v.username)')
            ->andwhere('v.username IN (:following)')
            ->setParameter('following', $users)
            ->getQuery()
            ->getSingleScalarResult();  
        
    }

    //affiche le total de membres que vous suivez
    public function countFollowing(Collection $users) {

        return $this->createQueryBuilder('v')
            ->select('count(DISTINCT v.username)')
            ->andwhere('v.username IN (:followers)')
            ->setParameter('followers', $users)
            ->getQuery()
            ->getSingleScalarResult();  
        
    }
    
    // récupère le nombre total de vues/user
    public function CountViews($user) {

        return $this->createQueryBuilder('v')
        ->select('SUM (v.views) as numberView')
        ->andWhere('v.username = :val')
        ->setParameter('val', $user)
        ->getQuery()
        ->getSingleScalarResult();
    }


    //retourne la vidéo la plus vue de l'user
    public function getMaxVideoByUser($user) {

        return $this->createQueryBuilder('v')
        ->andwhere('v.username = :val')
        ->setParameter('val', $user)
        ->orderBy('v.views', 'DESC') 
        ->setMaxResults(10) 
        ->getQuery() 
        ->getResult(); 
    }



    //permet de supprimer toutes les vidéos
    public function deleteAllVideos() {

        return $this->createQueryBuilder('v')
        ->delete()
        ->where('v.id != 0')
        ->getQuery()
        ->getResult();

    }


     //permet de supprimer toutes les vidéos
     public function deleteAllUserVideos($user) {

        return $this->createQueryBuilder('v')
        ->delete()
        ->where('v.id != 0')
        ->andwhere('v.username = :val')
        ->setParameter('val', $user)
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
