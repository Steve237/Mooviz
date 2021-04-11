<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Users;
use App\Entity\Avatar;
use App\Entity\Videos;
use App\Entity\Webhook;
use App\Form\UsersType;
use App\Form\AvatarType;
use App\Repository\UsersRepository;
use App\Repository\VideoRepository;
use App\Repository\AvatarRepository;
use App\Repository\VideosRepository;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    public function userProfile(UsersRepository $repo, PlaylistRepository $repository, VideosRepository $videorepo, AvatarRepository $repoAvatar)
    {
        //Permet d'afficher l'avatar et username dans espace perso

        $username = new Users();
        $username = $this->getUser();

        $avatar = new Avatar();

        $avatars = $repoAvatar->findByUser($username);

        $playlists = $repository->showVideoByUser($username);
        $videos = $videorepo->getVideos();
        $current_user_videos = $videorepo->getVideoByUser($username);

        return $this->render('user/mainprofile.html.twig', [
            'avatars' => $avatars,
            'playlists' => $playlists,
            'videos' => $videos,
            'current_user_videos' => $current_user_videos

        ]);
    }

   

    /**
     * @Route("/main/user", name="userprofile")
     */
    public function showNewVideo(VideosRepository $videorepo)
    {
        //permet d'obtenir les 10 nouvelles vidéos
        $videos = $videorepo->getVideos();

        return $this->render('user/userprofile.html.twig', [
            
            'videos' => $videos
        ]);
    }


    /**
     * @Route("/main/userplaylist", name="user_playlist")
     * //permet de voir la playlist dans l'espace user
     */
    public function showPlaylist(PlaylistRepository $repository)
    {    
           $user = $this->getUser();
    
            $playlists = $repository->showVideoByUser($user);

            if(empty($playlists)){

                $this->addFlash('no_videos', 'Vous n\'avez ajouté aucune vidéo à votre playlist');
                return $this->redirectToRoute('userprofile'); 

            }
            
            return $this->render('user/userplaylist.html.twig', [
                "playlists" => $playlists
            ]);
        
    }

    /**
     * Permet de charger plus de vidéos dans la playlist
     * @Route("/loadMoreUserPlaylist/{start}", name="loadMoreUserPlaylist", requirements={"start": "\d+"})
     */
    public function loadMoreUserPlaylist(PlaylistRepository $repository, $start = 20)
    {   
        $user = $this->getUser();

        // on récupère les 10 prochaines vidéos
        $playlists = $repository->showVideoByUser($user);

        return $this->render('user/loadMoreUserPlaylist.html.twig', [
            
            'playlists' => $playlists,
            'start' => $start
        ]);
    }

    /**
     * @Route("/main/user_channels", name="user_channels")
     * //permet de voir la liste des chaines dans l'espace profil
     */
    public function showChannels(AvatarRepository $repoAvatar, UsersRepository $repoUser, Request $request, PaginatorInterface $paginator)
    {    
        $username = new Users();
        
        $user = $this->getUser();

        $follow = $user->getFollowing();
        
        $videos = new Videos();

        $avatars = $repoAvatar->findByUser($follow);

        $userChannel = $paginator->paginate(
            $repoUser->findUser($follow),
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/

        );


        //Si user n'a aucune chaine renvoie message flash pour l'informer
        if(empty($avatars)) {

            $this->addFlash('no_videos', 'Vous n\'etes abonné à aucune chaine pour le moment, veuillez vous abonnez afin de suivre vos contenus préférés.');
            return $this->redirectToRoute('userprofile');

        }

        return $this->render('user/profile_page_channels.html.twig', [
            "userChannel" => $userChannel,
            "user" => $user,
            "videos" => $videos,
            "avatars" => $avatars
        ]);
        
    }


    /**
     * @Route("/user_videos_channels", name="user_video_channels")
     * //permet d'accéder à la liste des vidéos des chaînes auxquelles on s'est abonné
     */
    public function showAllChannelVideos(TokenStorageInterface $tokenStorage, VideosRepository $videorepo, PaginatorInterface $paginator, Request $request) {

        $currentUser = $tokenStorage->getToken()->getUser();

        if($currentUser instanceof Users) {
            
            //recupère liste des vidéos des utilisateurs auxquels il est abonné
            
            $videos = $paginator->paginate(
                $videorepo->findAllByUsers($currentUser->getFollowing()),
                $request->query->getInt('page', 1), /*page number*/
                20 /*limit per page*/

            );
        
        } else {

            return $this->redirectToRoute('home');
        }

        $following_videos = $videorepo->findAllByUsers($currentUser->getFollowing());

        if(empty($following_videos)) {

            $this->addFlash('no_videos', 'Vous n\'etes abonné à aucune chaine pour le moment, veuillez vous abonnez afin de suivre vos contenus préférés.');
            return $this->redirectToRoute('userprofile');

        }


        return $this->render('user/user_videos_channels.html.twig', [
            'videos' => $videos,
     
            ]);
    
    }


    /**
     * @Route("/main/update_avatar", name="avatar_update")
     * //permet d'ajouter un avatar
     */
    public function userAvatar(Request $request, EntityManagerInterface $entity, AvatarRepository $repoAvatar) { 


        $avatar = new Avatar();

        $user = new Users();
        $user = $this->getUser();

        $form = $this->createForm(AvatarType::class, $avatar);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $avatarExist = $repoAvatar->findByUser($user);
            
            $file = $avatar->getAvatar();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('upload_directory'), $fileName);

            $date = new \DateTime();
            $avatar->setAvatar($fileName);
            $avatar->setUser($user);
            $avatar->setUpdatedAt($date);
            $entity->persist($avatar);
            $entity->flush();
            return $this->redirectToRoute("userprofile");

        }

        return $this->render('user/updateavatar.html.twig', [
            
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/main/update_avatar/{id}", name="update_image")
     * //permet de modifier un avatar
     */
    public function updateUser(Avatar $avatar, Request $request, EntityManagerInterface $entity) { 


        $form = $this->createForm(AvatarType::class, $avatar);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            
            $date = new \DateTime();
            $avatar->setUpdatedAt($date);
            $entity->persist($avatar);
            $entity->flush();
            
            return $this->redirectToRoute("userprofile");

        }

        return $this->render('user/updateavatar.html.twig', [
            
            'form' => $form->createView(),
            'avatar' => $avatar
        ]);
    }





    /**
     * @Route("/main/user_account/{id}", name="user_account")
     * //permet d'accéder et de modifier les infos du compte user
    */
    public function userAccount(Users $user, Request $request, EntityManagerInterface $entity, AvatarRepository $repoAvatar, UsersRepository $repoUser, UserPasswordEncoderInterface $encoder) { 

        
        $form = $this->createForm(UsersType::class, $user);

        $form->handleRequest($request);
        
        $avatars = $repoAvatar->findByUser($user);

        if($form->isSubmitted() && $form->isValid()) {
            
            $username = $user->getUsername();
            $email = $user->getEmail();

            $passwordCrypte = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($passwordCrypte);
            $user->setConfirmPassword($passwordCrypte);
            $entity->persist($user);
            $entity->flush();

            $stripe = new \Stripe\StripeClient(
                'sk_test_51HpdbCLfEkLbwHD1453jzn7Y69TdfWFJ9zzpYWSlL6Y7w3RgWgTOs7MQN91BzrP11C5jUquQFi1b8LK4GyIs10Gu00jH3iKTqe'
              );
              $stripe->customers->update(
                $user->getCustomerid(),
                ["name" => $username, "email" => $email]
              );
    
                $date = new \Datetime();
    
                $notification = new Webhook();
                $notification->setType('modification');
                $notification->setContent('modification des identifiants');  
                $notification->setCreatedAt($date);
                $notification->setUsername($username);
                $entity->persist($notification);
                $entity->flush();  

                $this->addflash('update_user_infos', 'Vos identifiants ont été modifiés avec succès.');
                return $this->redirectToRoute("userprofile");

        }

        return $this->render('user/useraccount.html.twig', [

            'form' => $form->createView(),
            'avatars' => $avatars,
            'user' => $user
        ]);
    }


    /**
     * @Route("/main/payment_info/{id}", name="user_payment
     * ")
     * //permet d'accéder et de modifier les infos du compte user
    */
    public function userPayment(Users $user) {


        return $this->render('user/userpayment.html.twig', [

            
        ]);



    }

    /**
     *permet d'afficher avatar
     */
    public function showAvatar(AvatarRepository $repoAvatar)
    {

        $username = new Users();
        $username = $this->getUser();

        $avatars = $repoAvatar->findByUser($username);

        return $this->render('user/avatar.html.twig', [
            'avatars' => $avatars,
     
            ]);
    }



    
    /**
     * @Route("/user_dashboard/{id}", name="user_dashboard")
     * //permet d'accéder au tableau de bord de l'user
    */
    public function userDashboard(Users $user, VideosRepository $videoRepo) { 

        $videoCount = $videoRepo->countUserVideos($user);

        $followerCount = $videoRepo->countFollower($user->getFollowers());

        $followingCount = $videoRepo->countFollowing($user->getFollowing());

        $viewCount = $videoRepo->CountViews($user);

        $maxVideoViews = $videoRepo->getMaxVideoByUser($user);

        $minVideoViews = $videoRepo->getMinVideoByUser($user);

        return $this->render('user/dashboard.html.twig', [

            "videoCount" => $videoCount,
            "followerCount" => $followerCount,
            "followingCount" => $followingCount,
            "viewCount" => $viewCount,
            "maxVideoViews" => $maxVideoViews,
            "minVideoViews" => $minVideoViews

        ]);
    }

    /**
    * @Route("/main/account_delete/{id}", name="account_delete")
    */
    public function deleteUser(Users $user, EntityManagerInterface $entity)
    {   
        $date = new \Datetime();

        $userConnected = $this->getUser();
        $userId = $userConnected->getId();
        
        $username = $user->getUsername();

        if($user->getId() != $userId) {

            return $this->redirectToRoute('connexion');
        }

        $notification = new Webhook();
        $notification->setType('suppression');
        $notification->setContent('suppression de compte');  
        $notification->setCreatedAt($date);
        $notification->setUsername($username);
        $entity->persist($notification);
        $entity->flush();  
        
        $stripe = new \Stripe\StripeClient(
            'sk_test_51HpdbCLfEkLbwHD1453jzn7Y69TdfWFJ9zzpYWSlL6Y7w3RgWgTOs7MQN91BzrP11C5jUquQFi1b8LK4GyIs10Gu00jH3iKTqe'
          );
          
          $stripe->customers->delete(
            
            $user->getCustomerid(),
            []
          );

        $entity->remove($user);
        $entity->flush();

        return $this->redirectToRoute('home');
        
    }

}
