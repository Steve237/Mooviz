<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Videos;
use App\Form\VideoType;
use App\Entity\Category;
use App\Form\UploadType;
use App\Form\CategoryType;
use App\Entity\Notifications;
use App\Repository\VideoRepository;
use App\Repository\VideosRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminController extends AbstractController
{
    /**
     * @Route("/upload", name="addvideo")
     * //Permet de télécharger des vidéos
     */
    public function AddVideo(Videos $video = null, Notifications $notifications = null, Request $request, EntityManagerInterface $entitymanager)
    {

        if (!$video) {

            $video = new Videos();
        }

        if (!$notifications) {

            $notifications = new Notifications();
        }

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

            $notifications->setVideo($video);
            $entitymanager->persist($notifications);
            $entitymanager->flush();

            return new JsonResponse('ok');
        }
            return new JsonResponse('err');
    }

        return $this->render('admin/admin.html.twig', [

            "form" => $form->createView(),
        ]);
    
    }

    /**
     * @Route("/update_video/{id}", name="update_video")
     * //Permet de télécharger des vidéos
     */
    public function UpdateVideo(Videos $video, Notifications $notifications = null, Request $request, EntityManagerInterface $entitymanager)
    {

        if (!$notifications) {

            $notifications = new Notifications();
        }

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

            $notifications->setVideo($video);
            $entitymanager->persist($notifications);
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
     *  @Route("/042491f448463ffa79e596a3333d0943", name="success_upload")
     * //cette fonction redirige vers la page des vidéos user avec message success
     * //route cryptée afin que personne ne puisse la deviner
     */
    public function showMessageSuccessUpload() {

        $this->addFlash('success', 'votre vidéo a été ajouté avec succès');
        
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

        return $this->render('admin/user_video.html.twig', [
            
            'videos' => $videos
            
        ]);

    }

     /**
     * @Route("/delete_video/{id}", name="delete_video")
     * //permet à l'user de voir les vidéos qu'il a ajouté
     */
    public function deleteVideo(Videos $video, EntityManagerInterface $entityManager) {

        $entityManager->remove($video);
        $entityManager->flush();

        $this->addFlash('success', 'votre vidéo a été supprimé avec succès');
        
        return $this->redirectToRoute('user_videos');
        
        
    }
    
    
    
    /**
     * 
     */
    public function showNotifications(NotificationsRepository $repo) {

        //permet afficher nombre notifications 

        $notifications = $repo->findAllNotification();

        $number = $repo->numberNotif();


        return $this->render('notifications/notifications.html.twig', [

            "notifications" => $notifications,
            "number" => $number
        ]);


    }
}