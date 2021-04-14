<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Videos;
use App\Entity\Notifications;
use App\Repository\UsersRepository;
use App\Repository\AvatarRepository;
use App\Repository\VideosRepository;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FollowingController extends AbstractController
{
    /**
     * @Route("/following/{id}", name="following")
     * //permet de s'abonner à un user
     */
    public function follow(Users $userToFollow, EntityManagerInterface $entity)
    {
        $currentUser = $this->getUser();
        $username = $currentUser->getUsername();

        $notification = new Notifications();

        if($userToFollow->getId() !== $currentUser) {

            $date_time = new \DateTime();
            
            $currentUser->getFollowing()->add($userToFollow);
            $entity->flush();
            
            $notification->setOrigin($currentUser);
            $notification->setDestination($userToFollow);
            $notification->setContent($username. " s'est abonné à votre chaine.");
            $notification->setDate($date_time);
            $notification->setType('newfollower');

            $entity->persist($notification);
            $entity->flush();

            return $this->json([
                'code' => 200, 
                'message' => 'user ajouté'
            ], 200);
        
        }

        return $this->redirectToRoute('home');
    }


    /**
     * @Route("/unfollowing/{id}", name="unfollowing")
     * //permet de se désabonner
     */
    public function unfollow(Users $userToUnFollow, EntityManagerInterface $entity)
    {
        $currentUser = $this->getUser();

            $currentUser->getFollowing()
            ->removeElement($userToUnFollow);

            $entity->flush();

            return $this->json([
                'code' => 200, 
                'message' => 'user ajouté'
            ], 200);
    }

     /**
     * @Route("/followed_videos_list", name="followed_videos_list")
     * //permet d'accéder à la liste des vidéos des chaînes auxquelles on s'est abonné
     */
    public function showFollowingVideo(TokenStorageInterface $tokenStorage, VideosRepository $videorepo, PaginatorInterface $paginator, Request $request) {

        $currentUser = $tokenStorage->getToken()->getUser();

        if($currentUser instanceof Users) {
            
            //recupère liste des vidéos des utilisateurs auxquels il est abonné
            
            $videos = $videorepo->findAllByUsers($currentUser->getFollowing());

        
        } else {

            return $this->redirectToRoute('home');
        }

        $following_videos = $videorepo->findAllByUsers($currentUser->getFollowing());

        if(empty($following_videos)) {

            $this->addFlash('no_channels', 'Vous n\'etes abonné à aucune chaine pour le moment, veuillez vous abonnez afin de suivre vos contenus préférés.');
            return $this->redirectToRoute('allvideos');

        }

        $loadMoreStart = 20;

        return $this->render('following/following_videos.html.twig', [
            'videos' => $videos,
            'loadMoreStart' => $loadMoreStart
     
            ]);
    
    }

    /**
     * @Route("/channels", name="channels_list")
     * //permet d'accéder à la liste des chaînes auxquelles on s'est abonné
     */
    public function listChannel(AvatarRepository $repoAvatar, UsersRepository $repoUser){

        
        $user = $this->getUser();

        $follow = $user->getFollowing();
        
        $videos = new Videos();

        $avatars = $repoAvatar->findByUser($follow);

        $userChannel = $repoUser->findUser($follow);
        
        //Si user n'a aucune chaine renvoie message flash pour l'informer
        if(empty($avatars)) {

            $this->addFlash('no_channels', 'Vous n\'etes abonné à aucune chaine pour le moment, veuillez vous abonnez afin de suivre vos contenus préférés.');
            return $this->redirectToRoute('allvideos');

        }

        $loadMoreStart = 20;

        return $this->render('following/following_channel.html.twig', [
            "userChannel" => $userChannel,
            "user" => $user,
            "videos" => $videos,
            "avatars" => $avatars,
            "loadMoreStart" => $loadMoreStart
        ]);
    }



     /**
     * @Route("/channel/{id}", name="channel")
     * //permet d'accéder à la liste des vidéos par chaine
     */
    public function Channel(Users $user, VideosRepository $repoVideo){

        $videos = $repoVideo->getVideoByUser($user);
        
        $totalVideosByUser = $repoVideo->countUserVideos($user);
        $loadMoreStart = 20;

        return $this->render('following/videos_by_channel.html.twig', [
            
            "videos" => $videos,
            "user" => $user,
            "totalVideosByUser" => $totalVideosByUser,
            "loadMoreStart" => $loadMoreStart
        ]);
    }


     /**
     * Permet de charger plus de vidéos dans la liste des vidéos des membres suivis
     * @Route("/loadMoreFollowingVideos/{start}", name="loadMoreFollowingVideos", requirements={"start": "\d+"})
     */
    public function loadMoreFollowingVideos(VideosRepository $videorepo, $start = 20)
    {   
        $user = $this->getUser();

        // on récupère les 20 prochaines vidéos
        $videos = $videorepo->findAllByUsers($user->getFollowing());


        return $this->render('following/loadMoreFollowingVideos.html.twig', [
            
            "videos" => $videos,
            'start' => $start
        ]);
    }


    /**
     * Permet de charger plus de chaines dans la liste des chaines
     * @Route("/loadMoreChannels/{start}", name="loadMoreChannels", requirements={"start": "\d+"})
     */
    public function loadMoreChannels(AvatarRepository $repoAvatar, UsersRepository $repoUser, $start = 20)
    {   
        $user = $this->getUser();

        $follow = $user->getFollowing();
        
        $videos = new Videos();

        $avatars = $repoAvatar->findByUser($follow);

        $userChannel = $repoUser->findUser($follow);

        return $this->render('following/loadMoreChannels.html.twig', [
            
            "userChannel" => $userChannel,
            "user" => $user,
            "videos" => $videos,
            "avatars" => $avatars,
            'start' => $start
        ]);
    }


    /**
     * Permet de charger plus de vidéos dans la liste des vidéos par chaine
     * @Route("/loadMoreVideosByChannel/{id}/{start}", name="loadMoreVideosByChannel", requirements={"start": "\d+"})
     */
    public function loadMoreVideoByChannel(Users $user, VideosRepository $repoVideo, $start = 20)
    {   
        $videos = $repoVideo->getVideoByUser($user);


        return $this->render('following/loadMoreVideosByChannel.html.twig', [
            
            "user" => $user,
            "videos" => $videos,
            'start' => $start
        ]);
    }

}
