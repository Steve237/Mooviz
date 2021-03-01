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


        $videos = $paginator->paginate(
            $repository->findAllWithPagination(Category::class), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );

        $firstBackgroundVideo = $repoBackground->findById(1);
        $secondBackgroundVideo = $repoBackground->findById(2);
    
        $categories = $repo->findAll();
        return $this->render('video/index.html.twig', [
            
            "videos" => $videos,
            "categories" => $categories,
            "firstBackgroundVideo" => $firstBackgroundVideo,
            "secondBackgroundVideo" => $secondBackgroundVideo
        ]);
    
    }

    
    /**
     * @Route("/main/videos/{category}", name="videobycategory")
     */
    public function videobyCategory(VideosRepository $repository, $category, CategoryRepository $repo, PaginatorInterface $paginator, Request $request, VideobackgroundRepository $repoBackground)
    {   
        
        $videos = $paginator->paginate(
            $repository->getVideoByCategory($category),
            $request->query->getInt('page', 1), /*page number*/
                20 /*limit per page*/
            );
        
        $firstBackgroundVideo = $repoBackground->findById(1);
        $secondBackgroundVideo = $repoBackground->findById(2);
        
        $categories = $repo->findAll();
        return $this->render('video/index.html.twig', [
            "videos" => $videos,
            "categories" => $categories,
            "firstBackgroundVideo" => $firstBackgroundVideo,
            "secondBackgroundVideo" => $secondBackgroundVideo
           
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
    * @Route("/main/addcomment/{id}/category/{idcategory}", name="addcomment")
    * @ParamConverter("video", options={"mapping": {"id" : "id"}})
    * @ParamConverter("category", options={"mapping": {"idcategory" : "id"}})
    */
    public function AddComment(Videos $video, Request $request, EntityManagerInterface $entity, Category $category) {

        $comments = new Comments();
        $user = $this->getUser();
        $username = $user->getUsername();

        $notification = new Notifications();
        
        $videoAuthor = $video->getUsername();
        $videoTitle = $video->getVideoTitle();

        $commentform = $this->createForm(CommentsType::class, $comments);
        $commentform->handleRequest($request);

        if($commentform->isSubmitted() && $commentform->isValid()) {

            $comments->setUsername($user);
            $date_time = new \DateTime();
            $comments->setDate($date_time);
            $comments->setVideo($video);

            $notification->setOrigin($user);
            $notification->setDestination($video->getUsername());
            $notification->setContent($username . " a ajouté un commentaire à votre vidéo: " .$videoTitle);
            $notification->setDate($date_time);
            $notification->setType("newcomment");

            $entity->persist($notification);
            $entity->flush();

            $this->addflash('success', 'votre commentaire a bien été ajouté');

            return $this->redirectToRoute('movie', ['id' => $video->getId(), 'idcategory' => $category->getId()]);


        }

       return $this->redirectToRoute('movie', ['id' => $video->getId(), 'idcategory' => $category->getId()]);
    }


    /**
    * Permet de modifer un commentaire
    * @Route("/update_comment/{id}/video/{idvideo}/category/{idcategory}", name="update_comment")
    * @ParamConverter("video", options={"mapping": {"idvideo" : "id"}})
    * @ParamConverter("category", options={"mapping": {"idcategory" : "id"}})
    */
    public function updateComment(Comments $comment, Videos $video, Category $category, EntityManagerInterface $entity, Request $request)
    {       
        if($request->isXmlHttpRequest()) {

            $usercomment = $request->request->get('commentaire');
            $comment->setContent($usercomment);
            $entity->persist($comment);
            $entity->flush();

            return $this->redirectToRoute('movie', ['id' => $video->getId(), 'idcategory' => $category->getId()]);


        }

        return $this->redirectToRoute('movie', ['id' => $video->getId(), 'idcategory' => $category->getId()]);
    }

    /**
     * Permet de supprimer commentaire
     * @Route("/delete_comment/{id}/video/{idvideo}/category/{idcategory}", name="delete_comment")
     * @ParamConverter("video", options={"mapping": {"idvideo" : "id"}})
     * @ParamConverter("category", options={"mapping": {"idcategory" : "id"}})
     */
    public function deleteComment(Comments $comment, Videos $video, Category $category, EntityManagerInterface $entity)
    {
        $entity->remove($comment);
        $entity->flush();
        
        return $this->redirectToRoute('movie', ['id' => $video->getId(), 'idcategory' => $category->getId()]);

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