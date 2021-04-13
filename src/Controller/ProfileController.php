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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/upload", name="addvideo")
     * //Permet d'ajouter des vidéos
     */
    public function AddVideo(Videos $video = null, Request $request, EntityManagerInterface $entitymanager, ValidatorInterface $validator)
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
    public function userVideo(VideosRepository $videorepo) {

        $user = new Users();
        $userName = $this->getUser();

        $videos = $videorepo->getVideoByUser($userName);
        
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
     * Permet à l'user de charger plus de vidéos dans sa liste
     * @Route("/loadMoreUserVideos/{start}", name="loadMoreUserVideos", requirements={"start": "\d+"})
     */
    public function loadMoreUserVideos(VideosRepository $videorepo, $start = 20)
    {   
        $user = $this->getUser();

        // on récupère les 20 prochaines vidéos
        $videos = $videorepo->getVideoByUser($user);

        return $this->render('admin/loadMoreUserVideos.html.twig', [
            
            'videos' => $videos,
            'start' => $start
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
     * permet d'afficher toutes les notifications sur écran classique
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
    * Permet de charger plus de notifications
    * @Route("/loadMoreUserNotifications/{start}", name="loadMoreUserNotifications", requirements={"start": "\d+"})
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
    * Permet de charger plus de notifications pour les mobiles
    * @Route("/loadMoreMobileNotifications/{start}", name="loadMoreMobileNotifications", requirements={"start": "\d+"})
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
     * //permet de supprimer toutes les notifications de l'user
     */
    public function deleteAllNotifications(NotificationsRepository $repo, EntityManagerInterface $entity) {

        $currentUser = $this->getUser(); 

        $repo->deleteAllNotif($currentUser);

        return $this->redirectToRoute('homepage');

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

            $videos = $paginator->paginate(
            $repository->userVideoSearch($query, $user),
            $request->query->getInt('page', 1),
            20 /*limit per page*/
        );

            if ($repository->userVideoSearch($query, $user) == null) {

                return $this->render('user/no_user_videos.html.twig');
            }

            else {


                return $this->render('user/user_videos_result.html.twig', [

                    'videos' => $videos
        
                ]);

            }

        }
    }














}