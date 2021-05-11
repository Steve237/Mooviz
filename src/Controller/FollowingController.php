<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Videos;
use App\Entity\Notifications;
use App\Repository\UsersRepository;
use App\Repository\AvatarRepository;
use App\Repository\VideosRepository;
use App\Repository\PlaylistRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FollowingController extends AbstractController
{
    /**
     * @Route("/main/following/{id}", name="following")
     * //permet de s'abonner à un user
     */
    public function follow(Users $userToFollow, EntityManagerInterface $entity)
    {
        // on récupère l'user connecté
        $currentUser = $this->getUser();
        $username = $currentUser->getUsername();

        // on crée une nouvelle instance de notifications
        $notification = new Notifications();

        // on vérifie si l'user à suivre est différent de celui qui veut le suivre
        if($userToFollow->getId() !== $currentUser) {

            $date_time = new \DateTime();
            
            // On réalise la jointure entre les deux
            $currentUser->getFollowing()->add($userToFollow);
            $entity->flush();
            
            // on envoie une notif à l'user suivi pour indiquer qu'il a un nouveau membre qui le suit
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
     * @Route("/main/unfollowing/{id}", name="unfollowing")
     * //permet de se désabonner d'un user
     */
    public function unfollow(Users $userToUnFollow, EntityManagerInterface $entity)
    {
        $currentUser = $this->getUser();

            $currentUser->getFollowing()
            ->removeElement($userToUnFollow);

            $entity->flush();

            return $this->json([
                'code' => 200, 
                'message' => 'abonnement annulé'
            ], 200);
    }

     /**
     * @Route("/main/followed_videos_list", name="followed_videos_list")
     * //permet d'accéder à la liste des vidéos des chaînes auxquelles on s'est abonné
     */
    public function showFollowingVideo(TokenStorageInterface $tokenStorage, VideosRepository $videorepo) {

        $currentUser = $tokenStorage->getToken()->getUser();

        if($currentUser instanceof Users) {
            
            //recupère liste des vidéos des utilisateurs auxquels il est abonné
            
            $videos = $videorepo->findAllByUsers($currentUser->getFollowing());

        
        } else {

            return $this->redirectToRoute('home');
        }


        if(empty($videos)) {

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
     * @Route("/main/channels", name="channels_list")
     * //permet d'accéder à la liste des chaînes auxquelles on s'est abonné
     */
    public function listChannel(VideosRepository $repo) {

        
        $user = $this->getUser();

        // récupère les utilisateurs auxquels il est abonné
        $follow = $user->getFollowing();

        // récupère les chaines auxquels il est abonné
        $userChannels = $repo->findAllByUsers($follow);
        
        //Si user n'est abonné à aucune chaine renvoie message flash pour l'informer
        if(empty($userChannels)) {

            $this->addFlash('no_channels', 'Vous n\'etes abonné à aucune chaine pour le moment, veuillez vous abonnez afin de suivre vos contenus préférés.');
            return $this->redirectToRoute('allvideos');

        }

        $loadMoreStart = 20;

        return $this->render('following/following_channel.html.twig', [
            "user" => $user,
            "loadMoreStart" => $loadMoreStart
        ]);
    }



     /**
     * @Route("/main/channel/{id}", name="channel")
     * //permet d'accéder à la liste des vidéos par chaine
     */
    public function Channel(Users $user, VideosRepository $repoVideo) {

        $userName = $this->getUser();

        // récupère les utilisateurs auxquels il est abonné
        $follow = $userName->getFollowing();

        $userChannels = $repoVideo->findAllByUsers($follow);

        //Si user n'est abonné à aucune chaine renvoie message flash pour l'informer
        if(empty($userChannels)) {

            $this->addFlash('no_channels', 'Vous n\'etes abonné à aucune chaine pour le moment, veuillez vous abonnez afin de suivre vos contenus préférés.');
            return $this->redirectToRoute('allvideos');

        }

        // récupère toutes les vidéos de l'user à qui appartient la chaine
        $videos = $repoVideo->getVideoByUser($user);
        
        // récupère total de vidéos d'un user
        $totalVideosByUser = $repoVideo->countUserVideos($user);

        // minimum de vidéos pour afficher button load more
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
     * @Route("/main/loadMoreFollowingVideos/{start}", name="loadMoreFollowingVideos", requirements={"start": "\d+"})
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
     * @Route("/main/loadMoreChannels/{start}", name="loadMoreChannels", requirements={"start": "\d+"})
     */
    public function loadMoreChannels(AvatarRepository $repoAvatar, $start = 20)
    {   
        $user = $this->getUser();

        return $this->render('following/loadMoreChannels.html.twig', [
            
            "user" => $user,
            'start' => $start
        ]);
    }


    /**
     * Permet de charger plus de vidéos dans la liste des vidéos par chaine
     * @Route("/main/loadMoreVideosByChannel/{id}/{start}", name="loadMoreVideosByChannel", requirements={"start": "\d+"})
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
