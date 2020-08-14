<?php

namespace App\Controller;

use App\Entity\Videos;
use App\Entity\Category;
use App\Repository\VideosRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VideoController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homePage(VideosRepository $repository, CategoryRepository $repo)
    {   
        $videos = $repository->findAll(Category::class);
        $categories = $repo->findAll();
        return $this->render('video/index.html.twig', [
            "videos" => $videos,
            "categories" => $categories
        ]);
    }

    /**
     * @Route("/videos/{category}", name="videobycategory")
     */
    public function videobyCategory(VideosRepository $repository, $category, CategoryRepository $repo)
    {   
        $videos = $repository->getVideoByCategory($category);
        $categories = $repo->findAll();
        return $this->render('video/index.html.twig', [
            "videos" => $videos,
            "categories" => $categories
           
        ]);
    }



      /**
     * @Route("/listmovies/{category}", name="moviebycategory")
     */
    public function moviebyCategory(VideosRepository $repository, $category, CategoryRepository $repo)
    {   
        $videos = $repository->getVideoByCategory($category);
        $categories = $repo->findAll();
        return $this->render('video/listmovie.html.twig', [
            "videos" => $videos,
            "categories" => $categories
           
        ]);
    }



    /**
    * @Route("/movie/{id}", name="movie")
    */
    public function movie(Videos $video)
    {   
        return $this->render('video/singlemovie.html.twig', [
            "video" => $video
        ]);
    }


    /**
    * @Route("/listvideo", name="allvideos")
    */
    public function listVideo(VideosRepository $repository, CategoryRepository $repo)
    {   
        $videos = $repository->findAll(Category::class);
        $categories = $repo->findAll();

        return $this->render('video/listmovie.html.twig', [
            "categories" => $categories,
            "videos" => $videos
        ]);
    }


}
