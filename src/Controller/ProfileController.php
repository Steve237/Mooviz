<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Videos;
use App\Form\VideoType;
use App\Entity\Category;
use App\Form\UploadType;
use App\Form\CategoryType;
use App\Entity\Notifications;
use App\Form\VideoDescriptionType;
use App\Repository\VideoRepository;
use App\Repository\VideosRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfileController extends AbstractController
{


    /**
     * @Route("/main/user_videos", name="user_videos")
     * permet à l'user de voir les vidéos qu'il a ajouté
     */
    public function userVideo(VideosRepository $videorepo) {

        
        $userName = $this->getUser();


        // récupère toutes les vidéos de l'user
        $videos = $videorepo->getVideoByUser($userName);

        // minimum de vidéos à partir duquel s'affiche bouton load more
        $loadMoreStart = 20;
        
        // nombre total videos de l'user
        $totalUserVideos = $videorepo->countUserVideos($userName);

        return $this->render('user/user_video.html.twig', [
            
            'videos' => $videos,
            'loadMoreStart' => $loadMoreStart,
            'totalUserVideos' => $totalUserVideos
            
        ]);

    }


     /**
     * Permet à l'user de charger plus de vidéos dans sa liste
     * @Route("/main/loadMoreUserVideos/{start}", name="loadMoreUserVideos", requirements={"start": "\d+"})
     */
    public function loadMoreUserVideos(VideosRepository $videorepo, $start = 20)
    {   
        $user = $this->getUser();

        // on récupère les 20 prochaines vidéos
        $videos = $videorepo->getVideoByUser($user);

        return $this->render('user/loadMoreUserVideos.html.twig', [
            
            'videos' => $videos,
            'start' => $start
        ]);
    }


    
    /**
     * permet d'afficher toutes les notifications sur écran classique
     */
    public function showNotifications(NotificationsRepository $repo) {

        $currentUser = $this->getUser(); 

        $notifications = $repo->findAllNotification($currentUser);

        $number = $repo->numberNotif($currentUser);

        $loadMoreStart = 10;


        return $this->render('notifications/notifications.html.twig', [

            "notifications" => $notifications,
            "number" => $number,
            "loadMoreStart" => $loadMoreStart
        ]);


    }

    /**
    * Permet de charger plus de notifications
    * @Route("/main/loadMoreUserNotifications/{start}", name="loadMoreUserNotifications", requirements={"start": "\d+"})
    */
    public function loadMoreUserNotifications(NotificationsRepository $repo, $start = 10)
    {   
        $currentUser = $this->getUser(); 

        // On récupère les prochaines notifications
        $notifications = $repo->findAllNotification($currentUser);

        return $this->render('notifications/loadMoreUserNotifications.html.twig', [
            
            'notifications' => $notifications,
            'start' => $start
        ]);
    }

    /**
     * //permet d'afficher nombre notifications sur mobile et lien vers la page mobile notifs
     */
    public function showNotificationsOnMobile(NotificationsRepository $repo) {

        $currentUser = $this->getUser(); 

        $number = $repo->numberNotif($currentUser);

        $notifications = $repo->findAllNotification($currentUser);


        return $this->render('notifications/mobilenotifications.html.twig', [

            "number" => $number,
            "notifications" => $notifications
        ]);

    }

    /**
     * //permet d'afficher toutes les notifications sur mobile
     * @Route("/main/mobile_notifications", name="mobile_notifications")
     */
    public function showNotifOnMobile(NotificationsRepository $repo) {

        $currentUser = $this->getUser(); 

        $notifications = $repo->findAllNotification($currentUser);
        
        
        if(empty($notifications)) {

            return $this->redirectToRoute('homepage');

        }

        $loadMoreStart = 10;

        return $this->render('notifications/listnotifications.html.twig', [

            "notifications" => $notifications,
            "loadMoreStart" => $loadMoreStart
        ]);

    }

    /**
    * Permet de charger plus de notifications pour les mobiles
    * @Route("/main/loadMoreMobileNotifications/{start}", name="loadMoreMobileNotifications", requirements={"start": "\d+"})
    */
    public function loadMoreMobileNotifications(NotificationsRepository $repo, $start = 10)
    {   
        $currentUser = $this->getUser(); 

        // On récupère les prochaines notifications
        $notifications = $repo->findAllNotification($currentUser);

        return $this->render('notifications/loadMoreMobileNotifications.html.twig', [
            
            'notifications' => $notifications,
            'start' => $start
        ]);
    }

    /**
     * @Route("/main/notification_delete/{id}", name="notif_delete")
     * //permet de supprimer la notification marqué comme vu
     */
    public function deleteNotifications(Notifications $notification, EntityManagerInterface $entity, Request $request) {


            $entity->remove($notification);
            $entity->flush();

            $this->addFlash('delete-notif', 'La notification a été supprimé avec succès');
            return $this->redirectToRoute('allvideos');


    }

    
    /**
     * @Route("/main/notifications_delete", name="notif_delete_all")
     * //permet de supprimer toutes les notifications en un clic 
     */
    public function deleteAllNotifications(NotificationsRepository $repo, EntityManagerInterface $entity) {

        $currentUser = $this->getUser(); 

        $repo->deleteAllNotif($currentUser);

        $this->addFlash('delete-all-notifs', 'Suppression de toutes les notifications réussie.');
            
        return $this->redirectToRoute('allvideos');

    }


    /**
     * //retourne le formulaire de recherche de vidéos dans l'espace membre
    */
    public function searchUserVideos() {
    
        $form = $this->createFormBuilder(null)
            
        ->setAction($this->generateUrl("user_videos_search"))
            ->add('query', TextType::class)

            ->getForm();
        
        
        return $this->render('user/user_video_search.html.twig', [


            'form' => $form->createView()

        ]);

    }

    /**
     * //traite la requête envoyé dans le formulaire de recherche.
     * @Route("/main/user_videos_search", name="user_videos_search")
     *
     */
    public function userVideoSearch(Request $request, VideosRepository $repository, PaginatorInterface $paginator) {

        $user = $this->getUser();
        $query = $request->request->get('form')['query'];
        
        if ($query) {

            $videos = $repository->userVideoSearch($query, $user);
        

            if ($repository->userVideoSearch($query, $user) == null) {

                return $this->render('user/no_user_videos.html.twig');
            }

            else {

                $loadMoreStart = 20;

                return $this->render('user/user_videos_result.html.twig', [

                    'videos' => $videos,
                    'query' => $query,
                    'loadMoreStart' => $loadMoreStart
        
                ]);

            }

        }
    
    }


    /**
    * Permet de recharger résultat supplémentaire quand user cherche vidéo dans sa liste de vidéos
    * @Route("/main/loadMoreVideosResult/{query}/{start}", name="loadMoreVideosResult", requirements={"start": "\d+"})
    */
    public function loadMoreVideosResult(VideosRepository $repository, $query, $start = 20)
    {   
        $user = $this->getUser();

        // on charge plus de vidéos de l'user correspondant à la requête formulé dans la searchbar
        $videos = $repository->userVideoSearch($query, $user);


        return $this->render('user/loadMoreVideosResult.html.twig', [
            
            'videos' => $videos,
            'start' => $start
        ]);
    }


}