<?php

namespace App\Controller;

use App\Entity\Subscription;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', []);
    }

     /**
     * @Route("/tarif", name="tarif")
     */
    public function tarifs()
    {   
        
        //renvoie à la liste des tarifs
        return $this->render('subscription/tarif.html.twig');
    }

}
