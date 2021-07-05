<?php

namespace App\Controller;

use App\Entity\Notifications;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class NotificationsController extends AbstractController
{

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

}