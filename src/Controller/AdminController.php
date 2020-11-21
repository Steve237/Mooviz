<?php

namespace App\Controller;

use App\Entity\Videos;
use App\Form\VideoType;
use App\Entity\Notifications;
use App\Repository\NotificationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/creation", name="addvideo")
     */
    public function AddVideo(Videos $video = null, Notifications $notifications = null, Request $request, EntityManagerInterface $entitymanager)
    {

        if (!$video) {

            $video = new Videos();
        }

        if (!$notifications) {

            $notifications = new Notifications();
        }




        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entitymanager->persist($video);
            $entitymanager->flush();

            $notifications->setVideo($video);
            $entitymanager->persist($notifications);
            $entitymanager->flush();

            return $this->redirectToRoute("homepage");
        }


        return $this->render('admin/admin.html.twig', [

            "form" => $form->createView()
        ]);
    
    }


    public function showNotifications(NotificationsRepository $repo) {

        $notifications = $repo->findAllNotification();

        $number = $repo->numberNotif();


        return $this->render('notifications/notifications.html.twig', [

            "notifications" => $notifications,
            "number" => $number
        ]);


    }
}