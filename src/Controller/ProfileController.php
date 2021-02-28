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
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProfileController extends AbstractController
{
    /**
     * @Route("/upload", name="addvideo")
     * //Permet de télécharger des vidéos
     */
    public function AddVideo(Videos $video = null, Request $request, EntityManagerInterface $entitymanager)
    {

        if (!$video) {

            $video = new Videos();
        }

        $notification = new Notifications();

        $user = $this->getUser();

        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);
        
        if($request->isXmlHttpRequest()) {
        
            if ($form->isSubmitted() && $form->isValid()) {
            
            //upload de la vidéo
            $videoFile = $video->getVideoLink();
            $fileVideo = md5(uniqid()).'.'.$videoFile->guessExtension();
            $videoFile->move($this->getParameter('video_directory'), $fileVideo);

            $video->setVideoLink('/uploads/'.$fileVideo);
            
            $videoImage = $video->getVideoImage();
            $fileImage = md5(uniqid()).'.'.$videoImage->guessExtension();
            $videoImage->move($this->getParameter('image_directory'), $fileImage);

            $video->setVideoImage($fileImage);
            $video->setUsername($user);
            $entitymanager->persist($video);
            $entitymanager->flush();

            
            return new JsonResponse('ok');
        }
            return new JsonResponse('err');
    }

        return $this->render('admin/addvideo.html.twig', [

            "form" => $form->createView(),
        ]);
    
    }

    /**
     * @Route("/update_video_image/{id}", name="update_video_image")
     * //Permet de modifier image de la vidéo.
     */
    public function UpdateVideoImage(Videos $video, Request $request, EntityManagerInterface $entitymanager)
    {
        $updateform = $this->createForm(UploadType::class, $video);
        $updateform->handleRequest($request);
        
        if($request->isXmlHttpRequest()) {
        
            if ($updateform->isSubmitted() && $updateform->isValid()) {
            

            $videoImage = $video->getVideoImage();
            $fileImage = md5(uniqid()).'.'.$videoImage->guessExtension();
            $videoImage->move($this->getParameter('image_directory'), $fileImage);
            $video->setVideoImage($fileImage);

            $entitymanager->persist($video);
            $entitymanager->flush();


            return new JsonResponse('ok');
        }
            return new JsonResponse('err');
    }

        return $this->render('admin/update_video.html.twig', [

            "updateform" => $updateform->createView(),
            "video" => $video
        ]);
    
    }

    /**
     * @Route("/update_video_description/{id}", name="update_video_description")
     * //Permet de modifier la description de la vidéo
     */
    public function UpdateVideoDescription(Videos $video, Request $request, EntityManagerInterface $entitymanager)
    {
        $formvideodescription = $this->createForm(VideoDescriptionType::class, $video);
        $formvideodescription->handleRequest($request);
        
        if ($formvideodescription->isSubmitted() && $formvideodescription->isValid()) {
            
            $entitymanager->persist($video);
            $entitymanager->flush();
            return $this->redirectToRoute('success_update');
        }

        return $this->render('admin/update_video_description.html.twig', [

            "formvideodescription" => $formvideodescription->createView(),
            "video" => $video
        ]);
    
    }


    /**
     *  @Route("/upload_video_successfull", name="success_upload")
     * //cette fonction redirige vers la page des vidéos user avec message success
     * //route cryptée afin que personne ne puisse la deviner
     */
    public function showMessageSuccessUpload() {

        $this->addFlash('success', 'Votre vidéo a été ajouté avec succès.');
        
        return $this->redirectToRoute('user_videos');
        
    }


     /**
     * @Route("/update_video_successfull", name="success_update")
     */
    public function showMessageSuccessUpdateVideo() {

        $this->addFlash('success', 'Votre vidéo a été modifié avec succès.');
        
        return $this->redirectToRoute('user_videos');
        
    }

    /**
     * @Route("/user_videos", name="user_videos")
     * //permet à l'user de voir les vidéos qu'il a ajouté
     */
    public function userVideo(VideosRepository $videorepo, PaginatorInterface $paginator, Request $request) {

        $user = new Users();
        $userName = $this->getUser();

        $videos = $paginator->paginate(
            $videorepo->getVideoByUser($userName),
            $request->query->getInt('page', 1), /*page number*/
                20 /*limit per page*/
            );
        
        $user_videos =  $videorepo->getVideoByUser($userName);

        if(empty($user_videos)) {

            $this->addFlash('no_videos', 'Vous n\'avez ajoutez aucune vidéo pour le moment, merci d\'en ajoutez.');
            return $this->redirectToRoute('userprofile');


        }

        return $this->render('admin/user_video.html.twig', [
            
            'videos' => $videos
            
        ]);

    }

     /**
     * @Route("/delete_video/{id}", name="delete_video")
     * //permet à l'user de supprimer les vidéos qu'il a ajouté
     */
    public function deleteVideo(Videos $video, EntityManagerInterface $entityManager) {

        $entityManager->remove($video);
        $entityManager->flush();

        $this->addFlash('success', 'votre vidéo a été supprimé avec succès');
        
        return $this->redirectToRoute('user_videos');
        
        
    }
    
    /**
     * permet d'afficher toutes les notifications
     */
    public function showNotifications(NotificationsRepository $repo) {

        $currentUser = $this->getUser(); 

        $notifications = $repo->findAllNotification($currentUser);

        $number = $repo->numberNotif($currentUser);


        return $this->render('notifications/notifications.html.twig', [

            "notifications" => $notifications,
            "number" => $number
        ]);


    }

    /**
     * //permet d'afficher nombre notifications sur mobile
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
     * @Route("/mobile_notifications", name="mobile_notifications")
     */
    public function showNotifOnMobile(NotificationsRepository $repo) {

        $currentUser = $this->getUser(); 

        $notifications = $repo->findAllNotification($currentUser);

        return $this->render('notifications/listnotifications.html.twig', [

            "notifications" => $notifications
        ]);

    }


     /**
     * @Route("/notification_delete/{id}", name="notif_delete")
     * //permet de supprimer les notifications marqué comme vu
     */
    public function deleteNotifications(Notifications $notification, EntityManagerInterface $entity, Request $request) {


            $entity->remove($notification);
            $entity->flush();

            return $this->json([
                'code' => 200, 
                'message' => 'user ajouté'
            ], 200);


    }


    /**
     * @Route("/notifications_delete", name="notif_delete_all")
     * //permet de supprimer toutes les notifications
     */
    public function deleteAllNotifications(NotificationsRepository $repo, EntityManagerInterface $entity) {

        $currentUser = $this->getUser(); 

        $repo->deleteAllNotif($currentUser);

        return $this->redirectToRoute('homepage');

    }

}