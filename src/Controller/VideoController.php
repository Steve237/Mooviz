<?php

namespace App\Controller;


use App\Entity\Videos;
use App\Entity\Category;
use App\Entity\VideoLike;
use App\Repository\AvatarRepository;
use App\Repository\VideosRepository;
use App\Repository\CategoryRepository;
use App\Repository\VideoLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\VideobackgroundRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $videos = $repository->findAllPublicVideos();
        
        $totalVideos = $repository->countVideos();
        $loadMoreStart = 50;
           

        // retourne les vidéos à la une sur la page accueil
        $firstBackgroundVideo = $repoBackground->findById(1);
        $secondBackgroundVideo = $repoBackground->findById(2);
    
        $categories = $repo->findAll();
    

        return $this->render('video/videolist.html.twig', [
            
            "videos" => $videos,
            "categories" => $categories,
            "firstBackgroundVideo" => $firstBackgroundVideo,
            "secondBackgroundVideo" => $secondBackgroundVideo,
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

        $totalVideos = $repository->countVideosByCategory($category);
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
    * Affiche la liste des vidéos sur la page vidéo
    * @Route("/main/listvideo", name="allvideos")
    */
    public function listVideo(VideosRepository $repository, CategoryRepository $repo)
    {   
        $videos = $repository->findAllPublicVideos();
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
     * Affiche liste des vidéos par catégorie sur la page vidéo
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
     * @Route("/main/loadMoreVideos/{start}", name="loadMoreVideos", requirements={"start": "\d+"})
     */
    public function loadMoreVideos(VideosRepository $repo, $start = 50)
    {   
        // on récupère les 50 prochaines vidéos
        $videos = $repo->findAllPublicVideos();;

        return $this->render('video/loadMoreVideos.html.twig', [
            
            'videos' => $videos,
            'start' => $start
        ]);
    }

    /**
     * Permet de charger plus de vidéos par catégorie
     * @Route("/main/loadMoreVideosByCategory/{category}/{start}", name="loadMoreVideosByCategory", requirements={"start": "\d+"})
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
    public function movie(VideosRepository $repository, Videos $video, Category $category, $id, EntityManagerInterface $entity)
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
        $user_videos = $repository->showVideoByUserId($userId, $video);

        // récupère les nouvelles vidéos
        $newvideos = $repository->getVideos($video);

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
    * @Route("/main/switched_player/movie/{id}/category/{idcategory}", name="second_player")
    * @ParamConverter("video", options={"mapping": {"id" : "id"}})
    * @ParamConverter("category", options={"mapping": {"idcategory" : "id"}})
    * //Permet d'afficher contenu de la vidéo sur une autre page.
    */
    public function SwitchToSecondVideoPlayer(Videos $video, EntityManagerInterface $entity, Category $category, VideosRepository $repository, AvatarRepository $repoAvatar)
    {   
        $nbreview = $video->getViews();
        $nbreview++;
        $video->setViews($nbreview);
        $entity->persist($video);
        $entity->flush();

        $user = $this->getUser();

        // retourne video de l'user à qui appartiennt la vidéo en cours
        $user_videos = $repository->showVideoByUserId($user, $video);
        
        
        // retourne 10 dernières vidéos
        $newvideos = $repository->getVideos($video);

        $videos = $repository->showVideoByCategory($category, $video);

        return $this->render('video/second_player.html.twig', [
            "video" => $video,
            "newvideos" => $newvideos,
            "user_videos" => $user_videos,
            "videos" => $videos

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
            
        ->setAction($this->generateUrl("handlesearch"))
            ->add('query', TextType::class)

            ->getForm();
        
        
        return $this->render('video/search.html.twig', [


            'form' => $form->createView()

        ]);

    }

    /**
     * //traite la requête envoyé dans le formulaire de recherche
     * @Route("/main/handlesearch", name="handlesearch")
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
     * @Route("/main/loadMoreResult/{query}/{start}", name="loadMoreResult", requirements={"start": "\d+"})
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


}