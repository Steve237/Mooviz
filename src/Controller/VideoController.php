<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Videos;
use App\Entity\Category;
use App\Entity\Comments;
use App\Form\CommentType;
use App\Form\VideoSearchType;
use App\Repository\VideosRepository;
use App\Repository\CategoryRepository;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class VideoController extends AbstractController
{
    /**
     * @Route("/videolist", name="homepage")
     */
    public function homePage(VideosRepository $repository, CategoryRepository $repo, Request $request)
    {   
        
        $searchForm = $this->createForm(VideoSearchType::class);
        $searchForm->handleRequest($request);
        
        $donnees = $repository->findAll();
 
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
 
            $videoTitle = $searchForm->getData()->getVideoTitle();

            $donnees = $repository->search($videoTitle);


            if ($donnees == null) {
                
                $errormessage = "Aucune vidéo trouvée avec ce titre";
                $videos = $repository->search($videoTitle);
                return $this->render('video/noresult.html.twig', [
                "errormessage" => $errormessage,
                "searchForm" => $searchForm->createView(),
                "videos" => $videos

                ]);
           
            }

            else {

                $videos = $repository->search($videoTitle);
                return $this->render('video/showresult.html.twig', [
                "videos" => $videos,
                "searchForm" => $searchForm->createView()
        ]);


            }

    }
        $videos = $repository->findAll(Category::class);
        $categories = $repo->findAll();
        return $this->render('video/index.html.twig', [
            "videos" => $videos,
            "categories" => $categories,
            "searchForm" => $searchForm->createView()
        ]);
    
    }

    
    /**
     * @Route("/videos/{category}", name="videobycategory")
     */
    public function videobyCategory(VideosRepository $repository, $category, CategoryRepository $repo, Request $request)
    {   
        
        $searchForm = $this->createForm(VideoSearchType::class);
        $searchForm->handleRequest($request);
        
        $donnees = $repository->findAll();
 
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
 
            $videoTitle = $searchForm->getData()->getVideoTitle();

            $donnees = $repository->search($videoTitle);


            if ($donnees == null) {
                
                $errormessage = "Aucune vidéo trouvée avec ce titre";
                $videos = $repository->search($videoTitle);
                return $this->render('video/noresult.html.twig', [
                "errormessage" => $errormessage,
                "searchForm" => $searchForm->createView(),
                "videos" => $videos

                ]);
           
            }

            else {

                $videos = $repository->search($videoTitle);
                return $this->render('video/showresult.html.twig', [
                "videos" => $videos,
                "searchForm" => $searchForm->createView()
        ]);


            }

        }
        
        $videos = $repository->getVideoByCategory($category);
        $categories = $repo->findAll();
        return $this->render('video/index.html.twig', [
            "videos" => $videos,
            "categories" => $categories,
            "searchForm" => $searchForm->createView()
           
        ]);
    }


    /**
     * @Route("/listmovies/{category}", name="moviebycategory")
     */
    public function moviebyCategory(VideosRepository $repository, $category, CategoryRepository $repo, Request $request)
    {   

        $searchForm = $this->createForm(VideoSearchType::class);
        $searchForm->handleRequest($request);
        
        $donnees = $repository->findAll();
 
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
 
            $videoTitle = $searchForm->getData()->getVideoTitle();

            $donnees = $repository->search($videoTitle);


            if ($donnees == null) {
                
                $errormessage = "Aucune vidéo trouvée avec ce titre";
                $videos = $repository->search($videoTitle);
                return $this->render('video/noresult.html.twig', [
                "errormessage" => $errormessage,
                "searchForm" => $searchForm->createView(),
                "videos" => $videos

                ]);
           
            }

            else {

                $videos = $repository->search($videoTitle);
                return $this->render('video/showresult.html.twig', [
                "videos" => $videos,
                "searchForm" => $searchForm->createView()
            ]);


            }

        }

        $videos = $repository->getVideoByCategory($category);
        $categories = $repo->findAll();
        return $this->render('video/listmovie.html.twig', [
            "videos" => $videos,
            "categories" => $categories,
            "searchForm" => $searchForm->createView()
           
        ]);
    }



    /**
    * @Route("/movie/{id}/category/{idcategory}", name="movie")
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

        $searchForm = $this->createForm(VideoSearchType::class, $video);
        $searchForm->handleRequest($request);

        $comments = new Comments();

        $form = $this->createForm(CommentType::class, $comments);
        $form->handleRequest($request);

        $donnees = $repository->findAll();
 
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
 
            $videoTitle = $searchForm->getData()->getVideoTitle();

            $donnees = $repository->search($videoTitle);


            if ($donnees == null) {
                
                $errormessage = "Aucune vidéo trouvée avec ce titre";
                $videos = $repository->search($videoTitle);
                return $this->render('video/noresult.html.twig', [
                "errormessage" => $errormessage,
                "searchForm" => $searchForm->createView(),
                "videos" => $videos

                ]);
           
            }

            else {

                $videos = $repository->search($videoTitle);
                return $this->render('video/showresult.html.twig', [
                "videos" => $videos,
                "searchForm" => $searchForm->createView()
        ]);


            }

        }

        if ($form->isSubmitted() && $form->isValid()) {


            $date = new \DateTime();

            $comments->setVideo($video);
            $comments->setAuthor($this->getUser());
            $comments->setPublicationDate($date);
            $entity->persist($comments);
            $entity->flush();

            return $this->redirectToRoute('movie',  array('id' => $video->getId(), 'idcategory' => $category->getId()));

        }

        $videos = $repository->showVideoByCategory($category, $id);
        return $this->render('video/singlemovie.html.twig', [
            "videos" => $videos,
            "video" => $video,
            "category" => $category,
            "searchForm" => $searchForm->createView(),
            'form' => $form->createView(),
            'comments' => $comments
        ]);
    }


    /**
    * @Route("/listvideo", name="allvideos")
    */
    public function listVideo(VideosRepository $repository, CategoryRepository $repo, Request $request)
    {   
        
        $searchForm = $this->createForm(VideoSearchType::class);
        $searchForm->handleRequest($request);
        
        $donnees = $repository->findAll();
 
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
 
            $videoTitle = $searchForm->getData()->getVideoTitle();

            $donnees = $repository->search($videoTitle);


            if ($donnees == null) {
                
                $errormessage = "Aucune vidéo trouvée avec ce titre";
                $videos = $repository->search($videoTitle);
                return $this->render('video/noresult.html.twig', [
                "errormessage" => $errormessage,
                "searchForm" => $searchForm->createView(),
                "videos" => $videos

                ]);
           
            }

            else {

                $videos = $repository->search($videoTitle);
                return $this->render('video/showresult.html.twig', [
                "videos" => $videos,
                "searchForm" => $searchForm->createView()
        ]);


            }

        }
        
        $videos = $repository->findAll(Category::class);
        $categories = $repo->findAll();

        return $this->render('video/listmovie.html.twig', [
            "categories" => $categories,
            "videos" => $videos,
            "searchForm" => $searchForm->createView()
        ]);
    }

}
