<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    /**
     * @Route("/video", name="video")
     */
    public function index(CategoryRepository $repository)
    {   
        $categories = $repository->findAll();
        return $this->render('video/index.html.twig', [
            "categories" => $categories
        ]);
    }
}
