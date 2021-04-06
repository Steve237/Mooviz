<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Videos;
use DateTimeInterface;
use App\Entity\Category;
use App\Entity\Comments;
use App\Entity\Notifications;
use App\Entity\VideoLike;
use App\Form\CommentsType;
use App\Form\VideoSearchType;
use App\Repository\VideosRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentsRepository;
use App\Repository\VideoLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\AvatarRepository;
use App\Repository\VideobackgroundRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class VideoController extends AbstractController
{
    /**
     * @Route("/main/videolist", name="homepage")
     */
    public function homePage(VideosRepository $repository, VideobackgroundRepository $repoBackground, CategoryRepository $repo, PaginatorInterface $paginator, Request $request)
    {   


        $videos = $repository->findAll();
           

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
           
            
        ]);
    
    }

    /**
     * @Route("/main/videos/{category}", name="videobycategory")
     */
    public function videobyCategory(VideosRepository $repository, Category $category, CategoryRepository $repo, PaginatorInterface $paginator, Request $request, VideobackgroundRepository $repoBackground)
    {   
        
        $videos = $repository->getVideoByCategory($category);
        
        $firstBackgroundVideo = $repoBackground->findById(1);
        $secondBackgroundVideo = $repoBackground->findById(2);
        
        $categories = $repo->findAll();


        return $this->render('video/index.html.twig', [
            "videos" => $videos,
            "categories" => $categories,
            "firstBackgroundVideo" => $firstBackgroundVideo,
            "secondBackgroundVideo" => $secondBackgroundVideo,
            "category" => $category
           
        ]);
    }


    /**
    * @Route("/main/listvideo", name="allvideos")
    */
    public function listVideo(VideosRepository $repository, CategoryRepository $repo, PaginatorInterface $paginator, Request $request)
    {   
        
        $videos = $repository->findAll();
        
        
        $categories = $repo->findAll();

        return $this->render('video/listmovie.html.twig', [
            "categories" => $categories,
            "videos" => $videos,
        ]);
    }


    /**
     * @Route("/main/listmovies/{category}", name="moviebycategory")
     */
    public function moviebyCategory(VideosRepository $repository, Category $category, CategoryRepository $repo)
    {   
 
        $videos = $repository->getVideoByCategory($category);
        
        $categories = $repo->findAll();
        
        return $this->render('video/listmoviebycategory.html.twig', [
            "videos" => $videos,
            "categories" => $categories,
            "category" => $category
           
        ]);
    }


    /**
     * Permet de charger plus de vidéos
     * @Route("/loadMoreVideos/{start}", name="loadMoreVideos", requirements={"start": "\d+"})
     */
    public function loadMoreVideos(VideosRepository $repo, $start = 50)
    {   
        // on récupère les 10 prochaines vidéos
        $videos = $repo->findAll();

        return $this->render('video/loadMoreVideos.html.twig', [
            
            'videos' => $videos,
            'start' => $start
        ]);
    }

    /**
     * Permet de charger plus de vidéos de la categorie en cours
     * @Route("/loadMoreVideosByCategory/{category}/{start}", name="loadMoreVideosByCategory", requirements={"start": "\d+"})
     */
    public function loadMoreVideosByCategory(VideosRepository $repo, Category $category, $start = 50)
    {   
        // on récupère les 10 prochaines vidéos
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
    public function movie(VideosRepository $repository, AvatarRepository $repoAvatar, CommentsRepository $repoComment, Videos $video, Category $category, $id, EntityManagerInterface $entity, Request $request)
    {   
        $nbreview = $video->getViews();
        $nbreview++;
        $video->setViews($nbreview);
        $entity->persist($video);
        $entity->flush();
        
        $user = $this->getUser();
        $userId = $user->getId();

        $videos = $repository->showVideoByCategory($category, $id);
        
        $user_videos = $repository->showVideoByUserId($userId);

        $newvideos = $repository->getVideos();

        $comments = new Comments();


        $commentform = $this->createForm(CommentsType::class, $comments);

        $comments = $repoComment->findComment($video);

        return $this->render('video/singlemovie.html.twig', [
            "videos" => $videos,
            "video" => $video,
            "category" => $category,
            "newvideos" => $newvideos,
            "commentform" => $commentform->createView(),
            "comments" => $comments,
            "user_videos" => $user_videos

        ]);
    }

    /**
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
    public function handleSearch(Request $request, VideosRepository $repository, PaginatorInterface $paginator) {

        $query = $request->request->get('form')['query'];
        
        if ($query) {

            $videos = $paginator->paginate(
            $repository->search($query),
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );

            if ($repository->search($query) == null) {

                return $this->render('video/noresult.html.twig');
            }

            else {


                return $this->render('video/showresult.html.twig', [

                    'videos' => $videos
        
        
        
                ]);

            }

        }
    }
}