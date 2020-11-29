<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SubscriptionController extends AbstractController
{

    /**
     * @Route("/checkout", name="checkout")
     */
    public function starter(SessionInterface $session)
    {   
        if(!$session->get('inscription')) {


            return $this->redirectToRoute('tarif');

        }
        
        //renvoie à la page de paiement
        return $this->render('subscription/starter.html.twig');
    }


    /**
     * @Route("/premium", name="premium")
     */
    public function premium(SessionInterface $session, SubscriptionRepository $repo)
    {   
        //Verifie si session inscription existe, sinon renvoie à tarif
        if(!$session->get('inscription')) {


            return $this->redirectToRoute('tarif');

        }

        //Vérifie si paiement déjà réussie et renvoie à tarif si user revient à cette page suite paiement réussi
        $userMail = $session->get('email');        

        $successfull = $repo->verifToken($userMail);

        if($successfull) {

            return $this->redirectToRoute('tarif');


        }

        
        //renvoie à la page de paiement pour le pack premium
        return $this->render('subscription/premium.html.twig');
    }

    /**
     * @Route("/tarif", name="tarif")
     */
    public function tarif()
    {   
        
        //renvoie à la liste des tarifs
        return $this->render('subscription/tarif.html.twig', [

            'name' => Subscription::getPlanDataNames(),
            'price' => Subscription::getPlanDataPrices(),



        ]);
    }


    /**
    * @Route("/7b9eae84f7af073e9b667e57cac32ce0", name="successToken")
    */
    public function successToken() {

        $random = random_bytes(10);
        $token = md5($random);

        return $this->redirectToRoute('success', ['token' => $token]);


    }
    
    /**
     * @Route("/success/{token}", name="success")
     */
    public function success(\Swift_Mailer $mailer, SessionInterface $session, EntityManagerInterface $entity, $token)
    {   
        //si pas de token renvoie à la page tarif
        if(!$token) {

            return $this->redirectToRoute('tarif');

        }

        //si token généré lors inscription absent, retour à tarif
        if(!$session->get('activationToken')) {

            return $this->redirectToRoute('tarif');

        }
        
        //envoie mail activation compte avec token et email user obtenue lors de l'inscription
        $activationtoken = $session->get('activationToken');   
        $email = $session->get('email'); 


        $message = (new \Swift_Message('Nouveau compte'))
            // On attribue l'expéditeur
            ->setFrom('essonoadou@gmail.com')
            // On attribue le destinataire
            ->setTo($email)
            // On crée le texte avec la vue
            ->setBody($this->renderView('email/activation.html.twig', ['token' => $activationtoken]),
            'text/html'
            )
            ;
            $mailer->send($message);

            $this->addFlash('message', 'Votre message a été transmis, nous vous répondrons dans les meilleurs délais.');       

            //ajoute email du user dans table subscription si paiement réussi
            $subscription = $session->get('subscription'); 
            $subscription->setVerifpayment($email);
            $entity->persist($subscription);
            $entity->flush();

        return $this->render('subscription/success.html.twig');    
    }


    /**
     * @Route("/0a8b325a1197ab7d92c66b894c11ead2", name="redirect-error-premium")
     */
    public function pathErrorPremium() {

        $random = random_bytes(10);
        $token = md5($random);

        return $this->redirectToRoute('error_premium', ['token' => $token]);


    }

    /**
     * @Route("/error_payment_premium/{token}", name="error_premium")
     */
    public function errorPremium(SessionInterface $session, $token, SubscriptionRepository $repo) {


        //si pas de token renvoie à la page tarif
        if(!$token) {

            return $this->redirectToRoute('tarif');

        }

        //si token généré lors inscription absent, retour à tarif
        if(!$session->get('activationToken')) {

            return $this->redirectToRoute('tarif');

        }

         //Vérifie si paiement déjà réussie et renvoie à tarif si user revient à cette page suite paiement réussi
         $userMail = $session->get('email');        

         $successfull = $repo->verifToken($userMail);
 
         if($successfull) {
 
             return $this->redirectToRoute('tarif');
 
 
         }
 

         //renvoie à la page de paiement pour le pack premium
         return $this->render('subscription/errorpremium.html.twig');
    }
    
    
    
    
    /**
     * @Route("/b5bbafb55cff1accde53fa0c49d06747", name="redirect-error-starter")
    */ public function pathErrorStarter() {

        $random = random_bytes(10);
        $token = md5($random);

        return $this->redirectToRoute('error_starter', ['token' => $token]);


    }

    /**
     * @Route("/error_payment_starter/{token}", name="error_starter")
     */
    public function errorStarter(SessionInterface $session, $token, SubscriptionRepository $repo) {


        //si pas de token renvoie à la page tarif
        if(!$token) {

            return $this->redirectToRoute('tarif');

        }

        //si token généré lors inscription absent, retour à tarif
        if(!$session->get('activationToken')) {

            return $this->redirectToRoute('tarif');

        }

         //Vérifie si paiement déjà réussie et renvoie à tarif si user revient à cette page suite paiement réussi
         $userMail = $session->get('email');        

         $successfull = $repo->verifToken($userMail);
 
         if($successfull) {
 
             return $this->redirectToRoute('tarif');
 
 
         }
 
        //renvoie à la page de paiement pour le pack starter
         return $this->render('subscription/errorstarter.html.twig');
    }
    
}