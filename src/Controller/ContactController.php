<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request,\Swift_Mailer $mailer): Response
    {

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $contact = $form->getData();

            // On crée le message
            $message = (new \Swift_Message('Demande de renseignements'))
                // On attribue l'expéditeur
                ->setFrom($contact['email'])
                // On attribue le destinataire
                ->setTo('essonoadou@gmail.com')
                // On crée le texte avec la vue
                ->setBody(
                    $this->renderView(
                        'contact/message.html.twig', compact('contact')
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);

            $this->addFlash('success_contact_message', 'Votre message a été transmis, nous vous répondrons dans les meilleurs délais.');
        }

        return $this->render('contact/contact.html.twig', [
            
            'form' => $form->createView()
        ]);
    }
}
