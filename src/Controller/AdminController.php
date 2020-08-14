<?php

namespace App\Controller;

use App\Entity\Videos;
use App\Form\VideoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/creation", name="addvideo")
     */
    public function AddVideo(Videos $video = null, Request $request, EntityManagerInterface $entitymanager)
    {

        if (!$video) {

            $video = new Videos();
        }

        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entitymanager->persist($video);
            $entitymanager->flush();

            return $this->redirectToRoute("homepage");
        }


        return $this->render('admin/admin.html.twig', [

            "form" => $form->createView()
        ]);
    
    }

}
