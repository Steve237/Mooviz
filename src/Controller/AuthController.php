<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\ResetPassType;
use App\Entity\Subscription;
use App\Form\InscriptionType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class AuthController extends AbstractController
{
   

    /**
    * @Route("/activation/{token}", name="activation")
    */
    public function activation($token, UsersRepository $users, SessionInterface $session, EntityManagerInterface $entity)
    {
        // On recherche si un utilisateur avec ce token existe dans la base de données
        $user = $users->findOneBy(['activation_token' => $token]);

        // Si aucun utilisateur n'est associé à ce token
        if(!$user){
    
            // On génère un message
            $this->addFlash('message', 'Lien invalide:echec activation du compte');

            // On retourne à l'accueil
            return $this->redirectToRoute('home');
        }


        $stripe = new \Stripe\StripeClient('sk_test_51HpdbCLfEkLbwHD1453jzn7Y69TdfWFJ9zzpYWSlL6Y7w3RgWgTOs7MQN91BzrP11C5jUquQFi1b8LK4GyIs10Gu00jH3iKTqe');
          
        $stripe->subscriptions->create([
            
            'customer' => $user->getCustomerid(),
            'items' => [
              ['price' => 'price_1Hrd49LfEkLbwHD1TCJkouof']
            ],
            'trial_period_days' => 31,
            'collection_method' => 'charge_automatically'


        ]);
        
        //On indique que l'abonnement est souscrit
        // On supprime le token
        $user->setActivationToken(null);
        $user->setActivated(true);
        $user->setRoles('ROLE_USER');
        $entity->persist($user);
        $entity->flush();
        
        // On génère un message
        $this->addFlash('success', 'votre compte a été activé avec succès');

        // On retourne à l'accueil
        return $this->redirectToRoute('connexion');
    }

    /**
    * @Route("/connexion", name="connexion")
    */
    public function connexion(AuthenticationUtils $util)
    {

        return $this->render('auth/connexion.html.twig', [
            
            "lastUserName" => $util->getLastUsername(),
            
            "error" => $util->getLastAuthenticationError()
            
        ]);
    
    }

    /**
    * @Route("/deconnexion", name="deconnexion")
    */
    public function deconnexion()
    {
    
    }

    /**
    * @Route("/recoveryPass", name="recoveryPass")
    */
    public function recoveryPass(Request $request, UsersRepository $users, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        // On initialise le formulaire
        $form = $this->createForm(ResetPassType::class);

        // On traite le formulaire
        $form->handleRequest($request);

        // Si le formulaire est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les données
            $donnees = $form->getData();

            // On cherche un utilisateur ayant cet e-mail
            $user = $users->findOneByEmail($donnees['email']);

            // Si l'utilisateur n'existe pas
            if ($user === null) {
                // On envoie une alerte disant que l'adresse e-mail est inconnue
                $this->addFlash('danger', 'Cette adresse e-mail est inconnue');
            
                // On retourne sur la page de connexion
                return $this->redirectToRoute('connexion');
            }

            // On génère un token
            $token = $tokenGenerator->generateToken();

            // On essaie d'écrire le token en base de données
            try{
                $user->setResetToken($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('connexion');
            }

            // On génère l'URL de réinitialisation de mot de passe
            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            // On génère l'e-mail
            $message = (new \Swift_Message('Mot de passe oublié'))
                ->setFrom('essonoadou@gmail.com')
                ->setTo($user->getEmail())
                ->setBody($this->renderView('email/updatepass.html.twig', ['token' => $token]), 'text/html')
            ;

            // On envoie l'e-mail
            $mailer->send($message);

            // On crée le message flash de confirmation
            $this->addFlash('message', 'E-mail de réinitialisation du mot de passe envoyé !');

            // On redirige vers la page de login
            return $this->redirectToRoute('connexion');
        }

        // On envoie le formulaire à la vue
        return $this->render('auth/recoveryPassword.html.twig',['form' => $form->createView()]);
    }



    /**
    * @Route("/test", name="test")
    */
    public function test(SessionInterface $session) {

        //exemple utilisation token
        $foo = $session->get('activationToken');   
        
        var_dump($foo);
        die();


    }

    
    /**
    * @Route("/reset_pass/{token}", name="app_reset_password")
    */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
    {
        // On cherche un utilisateur avec le token donné
        $user = $this->getDoctrine()->getRepository(Users::class)->findOneBy(['reset_token' => $token]);

        // Si l'utilisateur n'existe pas
        if ($user === null) {
            // On affiche une erreur
            $this->addFlash('danger', 'Token Inconnu');
            return $this->redirectToRoute('connexion');
        }

        // Si le formulaire est envoyé en méthode post
        if ($request->isMethod('POST')) {
            // On supprime le token
            $user->setResetToken(null);

            // On chiffre le mot de passe
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $user->setconfirmPassword($passwordEncoder->encodePassword($user, $request->request->get('confirmPassword')));

            // On stocke
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // On crée le message flash
            $this->addFlash('message', 'Mot de passe mis à jour');

            // On redirige vers la page de connexion
            return $this->redirectToRoute('connexion');
        
        } else {
            // Si on n'a pas reçu les données, on affiche le formulaire
            return $this->render('auth/newPass.html.twig', ['token' => $token]);
        }

    }

}
