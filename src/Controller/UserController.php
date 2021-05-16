<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Users;
use App\Entity\Avatar;
use App\Entity\Webhook;
use App\Form\UsersType;
use App\Form\AvatarType;
use App\Repository\UsersRepository;
use App\Repository\AvatarRepository;
use App\Repository\VideosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    public function userProfile(AvatarRepository $repoAvatar)
    {
        //Permet d'afficher l'avatar et username dans espace perso
        $username = $this->getUser();

        $avatars = $repoAvatar->findByUser($username);

        return $this->render('user/mainprofile.html.twig', [
            
            'avatars' => $avatars

        ]);
    }



    /**
     * @Route("/main/user_channels", name="user_channels")
     * //permet de voir la liste des chaines dans l'espace profil
     */
    public function showChannels()
    {    
        
        $user = $this->getUser();

        $loadMoreStart = 20;

        return $this->render('user/profile_page_channels.html.twig', [
            "user" => $user,
            "loadMoreStart" => $loadMoreStart
        ]);
        
    }

    /**
     * Permet de charger plus de chaines dans la liste des chaines de l'espace user
     * @Route("/main/loadMoreUserChannels/{start}", name="loadMoreUserChannels", requirements={"start": "\d+"})
     */
    public function loadMoreUserChannels($start = 20)
    {   
        $user = $this->getUser();

        return $this->render('user/loadMoreUserChannels.html.twig', [
            
            "user" => $user,
            "start" => $start
        ]);
    }


    /**
     * @Route("/main/user_videos_channels", name="user_video_channels")
     * //permet d'accéder à la liste des vidéos des chaînes auxquelles on s'est abonné
     */
    public function showAllChannelVideos(TokenStorageInterface $tokenStorage, VideosRepository $videorepo) {

        $currentUser = $tokenStorage->getToken()->getUser();

        if($currentUser instanceof Users) {
            
            //recupère liste des vidéos des utilisateurs auxquels il est abonné
            
            $videos = $videorepo->findAllByUsers($currentUser->getFollowing());
        
        } else {

            return $this->redirectToRoute('home');
        }

        $loadMoreStart = 20;

        return $this->render('user/user_videos_channels.html.twig', [
            'videos' => $videos,
            'loadMoreStart' => $loadMoreStart
     
            ]);
    
    }


    /**
     * Permet de charger plus de vidéos dans la liste des vidéos des membres suivis
     * @Route("/main/loadMoreVideosChannels/{start}", name="loadMoreVideosChannels", requirements={"start": "\d+"})
     */
    public function loadMoreVideosChannels(TokenStorageInterface $tokenStorage, VideosRepository $videorepo, $start = 20)
    {   
        $currentUser = $tokenStorage->getToken()->getUser();

        if($currentUser instanceof Users) {

            // on récupère les 20 prochaines vidéos
            $videos = $videorepo->findAllByUsers($currentUser->getFollowing());
        
        }
        
        return $this->render('user/loadMoreChannelsVideos.html.twig', [
            
            'videos' => $videos,
            'start' => $start
        ]);
    }

    
    /**
     * @Route("/main/add_avatar", name="avatar_update")
     * //permet d'ajouter un avatar
     */
    public function userAvatar(Request $request, EntityManagerInterface $entity, AvatarRepository $repoAvatar) { 


        $avatar = new Avatar();

        $user = $this->getUser();

        $form = $this->createForm(AvatarType::class, $avatar);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $date = new \DateTime();
            $avatar->setUser($user);
            $avatar->setUpdatedAt($date);
            $entity->persist($avatar);
            $entity->flush();
            return $this->redirectToRoute("user_videos");

        }

        return $this->render('user/updateavatar.html.twig', [
            
            'form' => $form->createView(),
            'avatar' => $avatar
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
            
            return $this->redirectToRoute("user_videos");

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
            
            // on enregistre le contenu du formulaire en bdd
            $username = $user->getUsername();
            $email = $user->getEmail();

            $passwordCrypte = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($passwordCrypte);
            $user->setConfirmPassword($passwordCrypte);
            $entity->persist($user);
            $entity->flush();


            // On applique les modifications sur stripe
            $stripe = new \Stripe\StripeClient(
                'sk_test_51HpdbCLfEkLbwHD1453jzn7Y69TdfWFJ9zzpYWSlL6Y7w3RgWgTOs7MQN91BzrP11C5jUquQFi1b8LK4GyIs10Gu00jH3iKTqe'
              );
              $stripe->customers->update(
                $user->getCustomerid(),
                ["name" => $username, "email" => $email]
              );
    
                $date = new \Datetime();
    
                // on envoie notif à l'admin pour indiquer qu'un user a modifié son compte
                $notification = new Webhook();
                $notification->setType('modification');
                $notification->setContent('modification des identifiants');  
                $notification->setCreatedAt($date);
                $notification->setUsername($username);
                $entity->persist($notification);
                $entity->flush();  

                // message flash pour indiquer que les modifs ont bien été réalisés
                $this->addflash('update_user_infos', 'Vos identifiants ont été modifiés avec succès.');
                return $this->redirectToRoute("user_videos");

        }

        return $this->render('user/useraccount.html.twig', [

            'form' => $form->createView(),
            'avatars' => $avatars,
            'user' => $user
        ]);
    }


    /**
    * permet d'afficher avatar sur la barre de navigation
    */
    public function showAvatar(AvatarRepository $repoAvatar)
    {

        $username = $this->getUser();

        $avatars = $repoAvatar->findByUser($username);

        return $this->render('user/avatar.html.twig', [
            'avatars' => $avatars,
     
            ]);
    }



    
    /**
     * @Route("/main/user_dashboard/{id}", name="user_dashboard")
     * //permet d'accéder au tableau de bord de l'user
    */
    public function userDashboard(Users $user, VideosRepository $videoRepo) { 

        // nombre total de vidéos de l'user
        $videoCount = $videoRepo->countUserVideos($user);

        // nombre total de followers
        $followerCount = $videoRepo->countFollower($user->getFollowers());

        // nombre total de chaines auxquels il est abonné
        $followingCount = $videoRepo->countFollowing($user->getFollowing());

        // nombre total de vues
        $viewCount = $videoRepo->CountViews($user);

        // vidéos la plus vue
        $maxVideoViews = $videoRepo->getMaxVideoByUser($user);

        return $this->render('user/dashboard.html.twig', [

            "videoCount" => $videoCount,
            "followerCount" => $followerCount,
            "followingCount" => $followingCount,
            "viewCount" => $viewCount,
            "maxVideoViews" => $maxVideoViews,

        ]);
    }

    /**
    * @Route("/main/account_delete/{id}", name="account_delete")
    * // Permet de supprimer le compte de l'user
    */
    public function deleteUser(Users $user, EntityManagerInterface $entity, SessionInterface $session)
    {   
        $date = new \Datetime();

        $userConnected = $this->getUser();
        
        $username = $user->getUsername();

        // si utilisateur qui réalise action différent de l'user connecté
        if($user != $userConnected) {

            // on redirige vers la page accueil
            return $this->redirectToRoute('home');
        }

        // si tout est ok on envoie notif admin pour indiquer suppression de compte
        $notification = new Webhook();
        $notification->setType('suppression');
        $notification->setContent('suppression de compte');  
        $notification->setCreatedAt($date);
        $notification->setUsername($username);
        $entity->persist($notification);
        $entity->flush();  
        
        // On réalise la suppression du client sur Stripe
        $stripe = new \Stripe\StripeClient(
            'sk_test_51HpdbCLfEkLbwHD1453jzn7Y69TdfWFJ9zzpYWSlL6Y7w3RgWgTOs7MQN91BzrP11C5jUquQFi1b8LK4GyIs10Gu00jH3iKTqe'
          );
          
          $stripe->customers->delete(
            
            $user->getCustomerid(),
            []
          
        );

        // On le supprime aussi de la bdd
        $entity->remove($user);
        $entity->flush();

        // On redirige vers la page accueil
        $session = new Session();
        $session->invalidate();

        return $this->redirectToRoute('home');
        
    }

}
