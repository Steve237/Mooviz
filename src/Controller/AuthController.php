<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\InscriptionType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription")
     */
    public function index(Request $request, EntityManagerInterface $entity, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {
        $users = new Users();
        $form = $this->createForm(InscriptionType::class, $users);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $passwordCrypte = $encoder->encodePassword($users, $users->getPassword());
            $users->setPassword($passwordCrypte);
            $users->setConfirmPassword($passwordCrypte);
        
            $users->setActivationToken(md5(uniqid()));

            $entity->persist($users);
            $entity->flush();   
        
            $message = (new \Swift_Message('Nouveau compte'))
            // On attribue l'expéditeur
            ->setFrom('essonoadou@gmail.com')
            // On attribue le destinataire
            ->setTo($users->getEmail())
            // On crée le texte avec la vue
            ->setBody($this->renderView('email/activation.html.twig', ['token' => $users->getActivationToken()]),
            'text/html'
            )
            ;
            $mailer->send($message);

            $this->addFlash('message', 'Votre message a été transmis, nous vous répondrons dans les meilleurs délais.'); 
        
        }
        
        return $this->render('auth/inscription.html.twig', [
            "form" => $form->createView()
        ]);
    }

    /**
    * @Route("/activation/{token}", name="activation")
    */
    public function activation($token, UsersRepository $users)
    {
        // On recherche si un utilisateur avec ce token existe dans la base de données
        $user = $users->findOneBy(['activation_token' => $token]);

        // Si aucun utilisateur n'est associé à ce token
        if(!$user){
    
            // On génère un message
            $this->addFlash('message', 'Lien invalide:echec activation du compte');

            // On retourne à l'accueil
            return $this->redirectToRoute('accueil');
        }

        // On supprime le token
        $user->setActivationToken(null);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // On génère un message
        $this->addFlash('message', 'Utilisateur activé avec succès');

        // On retourne à l'accueil
        return $this->redirectToRoute('connexion');
    }

    /**
    * @Route("/connexion", name="connexion")
    */
    public function connexion()
    {

        return $this->render('auth/connexion.html.twig', [
            
        ]);
    
    }

     /**
    * @Route("/deconnexion", name="deconnexion")
    */
    public function deconnexion()
    {

        return $this->render('auth/connexion.html.twig', [
            
        ]);
    
    }





























































































































}
