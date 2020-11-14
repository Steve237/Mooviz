<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Videos;
use DateTimeInterface;
use App\Entity\Category;
use App\Entity\Comments;
use App\Entity\VideoLike;
use App\Form\CommentType;
use App\Form\VideoSearchType;
use App\Repository\VideosRepository;
use App\Repository\CategoryRepository;
use App\Repository\VideoLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    public function homePage(VideosRepository $repository, CategoryRepository $repo, PaginatorInterface $paginator, Request $request)
    {   


        $videos = $paginator->paginate(
            $repository->findAllWithPagination(Category::class), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );
    
        $categories = $repo->findAll();
        return $this->render('video/index.html.twig', [
            "videos" => $videos,
            "categories" => $categories
        ]);
    
    }

    
    /**
     * @Route("/main/videos/{category}", name="videobycategory")
     */
    public function videobyCategory(VideosRepository $repository, $category, CategoryRepository $repo, PaginatorInterface $paginator, Request $request)
    {   
        
        $videos = $paginator->paginate(
            $repository->getVideoByCategory($category),
            $request->query->getInt('page', 1), /*page number*/
                20 /*limit per page*/
            );
        
        
        $categories = $repo->findAll();
        return $this->render('video/index.html.twig', [
            "videos" => $videos,
            "categories" => $categories
           
        ]);
    }


    /**
     * @Route("/main/listmovies/{category}", name="moviebycategory")
     */
    public function moviebyCategory(VideosRepository $repository, $category, CategoryRepository $repo, PaginatorInterface $paginator, Request $request)
    {   
 
        $videos = $paginator->paginate(
        $repository->getVideoByCategory($category),
        $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );
        $categories = $repo->findAll();
        return $this->render('video/listmovie.html.twig', [
            "videos" => $videos,
            "categories" => $categories
           
        ]);
    }

    /**
    * @Route("/main/movie/{id}/category/{idcategory}", name="movie")
    * @ParamConverter("video", options={"mapping": {"id" : "id"}})
    * @ParamConverter("category", options={"mapping": {"idcategory" : "id"}})
    */
    public function movie(VideosRepository $repository, Videos $video, Category $category, $id, EntityManagerInterface $entity, Request $request)
    {   
        $nbreview = $video->getViews();
        $nbreview++;
        $video->setViews($nbreview);
        $entity->persist($video);
        $entity->flush();

        $videos = $repository->showVideoByCategory($category, $id);
        return $this->render('video/singlemovie.html.twig', [
            "videos" => $videos,
            "video" => $video,
            "category" => $category
        ]);
    }


    /**
    * @Route("/main/listvideo", name="allvideos")
    */
    public function listVideo(VideosRepository $repository, CategoryRepository $repo, PaginatorInterface $paginator, Request $request)
    {   
        
        $videos = $paginator->paginate(
            $repository->findAllWithPagination(Category::class), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );
        
        $categories = $repo->findAll();

        return $this->render('video/listmovie.html.twig', [
            "categories" => $categories,
            "videos" => $videos,
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
     * @Route("/main/searchbar", name="videosearch")
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
     * @Route("/handleSearch", name="handleSearch")
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

            if (!$videos) {

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