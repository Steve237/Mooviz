<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Comments;
use App\Form\CommentsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentsController extends AbstractController
{
    /**
     * @Route("/comments", name="comments")
     */
    public function AddComments(Comments $comments = null, Request $request, EntityManagerInterface $entitymanager)
    {   
        $username = new Users();
        
        $user = $this->getUser();

        if(!$comments) {


            $comments = new Comments();

        }

        $commentform = $this->createForm(CommentsType::class, $comments);
        $commentform->handleRequest($request);

        if($commentform->isSubmitted() && $commentform->isValid()) {

            $comments->setUsername($user);

            $date_time = new \DateTime();
            $comments->setDate($date_time);
            
            $entitymanager->persist($comments);
            $entitymanager->flush();


        }

        return $this->render('comments/comments.html.twig', [

            'commentform' => $commentform->createView()
        ]);
    }
}
