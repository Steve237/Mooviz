<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Videos;
use DateTimeInterface;
use App\Entity\Category;
use App\Entity\Comments;
use App\Entity\VideoLike;
use App\Form\CommentsType;
use App\Entity\Notifications;
use App\Form\VideoSearchType;
use App\Repository\AvatarRepository;
use App\Repository\VideosRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentsRepository;
use App\Repository\VideoLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\VideobackgroundRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class VideoController extends AbstractController
{
    /**
    * @Route("/main/videolist", name="homepage")
    * Affiche liste des vidéos sur la page accueil
    */
    public function homePage(VideosRepository $repository, VideobackgroundRepository $repoBackground, CategoryRepository $repo)
    {   
        $videos = $repository->findAll();
        
        $totalVideos = $repository->countVideos();
        $loadMoreStart = 50;
           

        $firstBackgroundVideo = $repoBackground->findById(1);
        $secondBackgroundVideo = $repoBackground->findById(2);
    
        $categories = $repo->findAll();
        

        $returnVideoByCategory = false;

        return $this->render('video/videolist.html.twig', [
            
            "videos" => $videos,
            "categories" => $categories,
            "firstBackgroundVideo" => $firstBackgroundVideo,
            "secondBackgroundVideo" => $secondBackgroundVideo,
            "returnVideoByCategory" => $returnVideoByCategory,
            "totalVideos" => $totalVideos,
            "loadMoreStart" => $loadMoreStart
           
            
        ]);
    
    }

    /**
     * Affiche liste des vidéos par catégorie sur la page accueil
     * @Route("/main/videos/{category}", name="videobycategory")
     */
    public function videobyCategory(VideosRepository $repository, Category $category, CategoryRepository $repo, VideobackgroundRepository $repoBackground)
    {   
        
        $videos = $repository->getVideoByCategory($category);
        
        $firstBackgroundVideo = $repoBackground->findById(1);
        $secondBackgroundVideo = $repoBackground->findById(2);
        
        $categories = $repo->findAll();

        $totalVideos = $repository->countVideos();
        $loadMoreStart = 50;

        return $this->render('video/videolistbycategory.html.twig', [
            "videos" => $videos,
            "categories" => $categories,
            "firstBackgroundVideo" => $firstBackgroundVideo,
            "secondBackgroundVideo" => $secondBackgroundVideo,
            "category" => $category,
            "totalVideos" => $totalVideos,
            "loadMoreStart" => $loadMoreStart
           
        ]);
    }


    /**
    * Affiche la liste des vidéos sur la page vidéos
    * @Route("/main/listvideo", name="allvideos")
    */
    public function listVideo(VideosRepository $repository, CategoryRepository $repo)
    {   
        $videos = $repository->findAll();
        $categories = $repo->findAll();

        $totalVideos = $repository->countVideos();
        $loadMoreStart = 50;

        return $this->render('video/listmovie.html.twig', [
            "categories" => $categories,
            "videos" => $videos,
            "totalVideos" => $totalVideos,
            "loadMoreStart" => $loadMoreStart
        ]);
    }


    /**
     * Affiche liste des vidéos par catégories sur la page vidéos
     * @Route("/main/listmovies/{category}", name="moviebycategory")
     */
    public function moviebyCategory(VideosRepository $repository, Category $category, CategoryRepository $repo)
    {   
 
        $videos = $repository->getVideoByCategory($category);
        $count = $repository->countVideos();
        
        $categories = $repo->findAll();
        $totalVideos = $repository->countVideos();
        $loadMoreStart = 50;
        
        return $this->render('video/listmoviebycategory.html.twig', [
            "videos" => $videos,
            "categories" => $categories,
            "category" => $category,
            "count" => $count,
            "totalVideos" => $totalVideos,
            "loadMoreStart" => $loadMoreStart
           
        ]);
    }


    /**
     * Permet de charger plus de vidéos dans la page accueil
     * @Route("/loadMoreVideos/{start}", name="loadMoreVideos", requirements={"start": "\d+"})
     */
    public function loadMoreVideos(VideosRepository $repo, $start = 50)
    {   
        // on récupère les 50 prochaines vidéos
        $videos = $repo->findAll();

        return $this->render('video/loadMoreVideos.html.twig', [
            
            'videos' => $videos,
            'start' => $start
        ]);
    }

    /**
     * Permet de charger plus de vidéos par catégorie
     * @Route("/loadMoreVideosByCategory/{category}/{start}", name="loadMoreVideosByCategory", requirements={"start": "\d+"})
     */
    public function loadMoreVideosByCategory(VideosRepository $repo, Category $category, $start = 50)
    {   
        // on récupère les 50 prochaines vidéos
        $videos = $repo->getVideoByCategory($category);


        return $this->render('video/loadMoreVideos.html.twig', [
            
            'videos' => $videos,
            'start' => $start
        ]);
    }
    

    /**
    * @Route("/main/movie/{id}/category/{idcategory}", name="movie")
    * @ParamConverter("video", options={"mapping": {"id" : "id"}})
    * @ParamConverter("category", options={"mapping": {"idcategory" : "id"}})
    * //Affiche contenu d'une vidéo
    */
    public function movie(VideosRepository $repository, CommentsRepository $repoComment, Videos $video, Category $category, $id, EntityManagerInterface $entity)
    {   
        //ajoute une vue à chaque fois que la page de la vidéo est vu
        $nbreview = $video->getViews();
        $nbreview++;
        $video->setViews($nbreview);
        $entity->persist($video);
        $entity->flush();
        
        $user = $this->getUser();
        $userId = $user->getId();

        // affiche les vidéos de la catégorie en cours sauf la vidéo en cours
        $videos = $repository->showVideoByCategory($category, $id);
        
        // récupère les vidéos de l'user
        $user_videos = $repository->showVideoByUserId($userId);

        // récupère les nouvelles vidéos
        $newvideos = $repository->getVideos();

        return $this->render('video/singlemovie.html.twig', [
            
            "videos" => $videos,
            "video" => $video,
            "category" => $category,
            "newvideos" => $newvideos,
            "user_videos" => $user_videos

        ]);
    }

    /**
    * Permet de partager une vidéo
    * @Route("/share/movie/{id}", name="partage")
    * //Permet de partager une vidéo.
    */
    public function shareMovie(Videos $video, EntityManagerInterface $entity)
    {   
        $nbreview = $video->getViews();
        $nbreview++;
        $video->setViews($nbreview);
        $entity->persist($video);
        $entity->flush();

        return $this->render('video/sharemovie.html.twig', [
            "video" => $video,

        ]);
    }

    /**
    * Permet de basculer sur un autre lecteur vidéo
    * @Route("main/switched_player/movie/{id}/category/{idcategory}", name="second_player")
    * @ParamConverter("video", options={"mapping": {"id" : "id"}})
    * @ParamConverter("category", options={"mapping": {"idcategory" : "id"}})
    * //Permet d'afficher contenu de la vidéo sur une autre page.
    */
    public function SwitchToSecondVideoPlayer(Videos $video, EntityManagerInterface $entity, Category $category, VideosRepository $repository)
    {   
        $nbreview = $video->getViews();
        $nbreview++;
        $video->setViews($nbreview);
        $entity->persist($video);
        $entity->flush();

        $user = $this->getUser();
        $userId = $user->getId();

        $videos = $repository->showVideoByCategory($category, $video);

        
        $user_videos = $repository->showVideoByUserId($userId);

        $newvideos = $repository->getVideos();

        return $this->render('video/second_player.html.twig', [
            "video" => $video,
            "videos" => $videos,
            "newvideos" => $newvideos,
            "user_videos" => $user_videos

        ]);
    }


    /**
     * @Route("/main/video/{id}/like", name="video_like")
    */
    public function like(Videos $video, EntityManagerInterface $entity, VideoLikeRepository $likeRepo) : Response {

        $user = $this->getUser();

        //Permet de supprimer le like d'un utilisateur
        if ($video->isLikedByUser($user)) {

            $like = $likeRepo->findOneBy([
                
                'video' => $video,
                'user' => $user
            ]);

            $entity->remove($like);
            $entity->flush();

            return $this->json([

                'code' => 200,
                'message' => 'Like bien supprimé',
                'likes' => $likeRepo->count(['video' => $video])
            ], 200);

        }

        $like = new VideoLike();
        $like->setVideo($video);
        $like->setUser($user);

        $entity->persist($like);
        $entity->flush();

        return $this->json([
            'code' => 200, 
            'message' => 'like ok',
            'likes' => $likeRepo->count(['video' => $video])
        
        
        ], 200);
    }


 
    /**
     * //retourne le formulaire de recherche
    */
    public function searchVideo() {
    
        $form = $this->createFormBuilder(null)
            
        ->setAction($this->generateUrl("handleSearch"))
            ->add('query', TextType::class)

            ->getForm();
        
        
        return $this->render('video/search.html.twig', [


            'form' => $form->createView()

        ]);

    }

    /**
     * //traite la requête envoyé dans le formulaire de recherche
     * @Route("/main/handleSearch", name="handleSearch")
     *
     */
    public function handleSearch(Request $request, VideosRepository $repository) {

        $query = $request->request->get('form')['query'];
        
        if ($query) {

            $videos = $repository->search($query);
       
            if ($repository->search($query) == null) {

                return $this->render('video/noresult.html.twig');
            }

            else {

                return $this->render('video/showresult.html.twig', [

                    'videos' => $videos,
                    'query' => $query
        
                ]);

            }
        }
    }

    /**
     * Permet de charger plus de vidéos dans la page des résultats de recherche
     * @Route("/loadMoreResult/{query}/{start}", name="loadMoreResult", requirements={"start": "\d+"})
     */
    public function loadMoreResult(VideosRepository $repository, $query, $start = 20)
    {   
        // on récupère les 20 prochaines vidéos
        $videos = $repository->search($query);

        return $this->render('video/loadMoreResult.html.twig', [
            
            'videos' => $videos,
            'start' => $start
        ]);
    }


     /**
     * @Route("/main/upload", name="addvideo")
     * Permet d'ajouter des vidéos
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

        return $this->render('video/addvideo.html.twig', [

            "form" => $form->createView(),
        ]);
    
    }


    /**
     * @Route("/main/update_video_image/{id}", name="update_video_image")
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

        return $this->render('video/update_video.html.twig', [

            "updateform" => $updateform->createView(),
            "video" => $video
        ]);
    
    }

    /**
     * @Route("/main/update_video_description/{id}", name="update_video_description")
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

        return $this->render('video/update_video_description.html.twig', [

            "formvideodescription" => $formvideodescription->createView(),
            "video" => $video
        ]);
    
    }

    /**
     *  @Route("/main/upload_video_successfull", name="success_upload")
     * //cette fonction redirige vers la page des vidéos user avec message success
     * //route cryptée afin que personne ne puisse la deviner
     */
    public function showMessageSuccessUpload() {

        $this->addFlash('success', 'Votre vidéo a été ajouté avec succès.');
        
        return $this->redirectToRoute('user_videos');
        
    }


     /**
     * @Route("/main/update_video_successfull", name="success_update")
     */
    public function showMessageSuccessUpdateVideo() {

        $this->addFlash('success', 'Votre vidéo a été modifié avec succès.');
        
        return $this->redirectToRoute('user_videos');
        
    }

}