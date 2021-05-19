<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FaqsController extends AbstractController
{
    /**
     * @Route("/faqs", name="faqs")
     * Redirige vers la faqs
     */
    public function faqs(): Response
    {
        return $this->render('faqs/faqs.html.twig', [
            'controller_name' => 'FaqController',
        ]);
    }
}
