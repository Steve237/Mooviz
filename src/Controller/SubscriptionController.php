<?php

namespace App\Controller;

use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Customer;
use App\Entity\Users;
use Stripe\SetupIntent;
use Stripe\PaymentIntent;
use App\Entity\Subscription;
use App\Form\InscriptionType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SubscriptionController extends AbstractController
{
    
    
    /**
     * @Route("/tarif", name="tarif")
     */
    public function tarif()
    {   
        
        //renvoie à la liste des tarifs
        return $this->render('subscription/tarif.html.twig');
    }
    
    /**
     * //page inscription
     * @Route("/inscription/{plan}", name="inscription")
     */
    public function index(Request $request, EntityManagerInterface $entity, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer, SessionInterface $session, $plan)
    {   
        if( $request->isMethod('GET')  ) 
        {
            if($plan != 'starter' && $plan != 'premium') {


                return $this->redirectToRoute('tarif');


            }
        }
        
        $user = new Users();
        
        $form = $this->createForm(InscriptionType::class, $user);


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $passwordCrypte = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($passwordCrypte);
            $user->setConfirmPassword($passwordCrypte);
        
            $user->setActivationToken(md5(uniqid()));
            $user->setRoles('ROLE_AWAITING');

            $date = new \Datetime();
            $user->setCreatedAt($date);
            $user->setPlan($plan);
            $user->setActivated(false);

            $entity->persist($user);
            $entity->flush();   


            $userToken = $session->set('activationToken', $user->getActivationToken());
            $userMail = $session->set('email', $user->getEmail()); 
            
            return $this->redirectToRoute('payment', ['id' => $user->getId()]);
        
        }
        
        return $this->render('auth/inscription.html.twig', [
            "form" => $form->createView(),
            'clientSecret' => null,
            'plan' => $plan
        ]);
    }


    /**
     * //page de paiement
     * @Route("/payment/{id}", name="payment")
     */
    public function paymentLater(Users $user, SessionInterface $session, UsersRepository $repoUser, EntityManagerInterface $entity)
    {  
        $name = $user->getUserName(); 
        $email = $user->getEmail();
        $userId = $user->getId();
        $plan = $user->getPlan();
            
        if($user->getVerifsubscription() != null) {
            
            return $this->redirectToRoute('tarif');
        }

        Stripe::setApiKey('sk_test_51HpdbCLfEkLbwHD1453jzn7Y69TdfWFJ9zzpYWSlL6Y7w3RgWgTOs7MQN91BzrP11C5jUquQFi1b8LK4GyIs10Gu00jH3iKTqe');

        //On crée un nouveau client
        $customer = Customer::create(["name" => $name, "email" => $email]);

        //On créé un nouveau setupintent pour ce client afin de pouvoir le débiter plus tard.
        $intent = SetupIntent::create([
            'customer' => $customer->id
          ]);

        //On enregistre en base de données l'id du client afin de pouvoir l'utiliser plus tard
        $user->setCustomerid($customer->id);
        $entity->persist($user);
        $entity->flush();

        return $this->render('subscription/payment.html.twig' , [
            
            //On envoie le secret client à la vue afin de pouvoir collecter les détails de sa carte bleu
            'clientSecret' => $intent->client_secret,
            
            //indique que la méthode de paiement est carte bleu.
            'payment_method_types' => ['card'],

            'userId' => $userId,

            'plan' => $plan

        ]);

    }

    /**
    * //redirection vers la page de paiement réussi avec token pour sécuriser la route cryptée.
    * @Route("/7b9efxfre5856973e9b66ye57cac32ce0/{id}", name="success_payment")
    */
    public function redirectIfSuccessPayment(Users $user, SessionInterface $session, EntityManagerInterface $entity) {

        //On créé un token et on l'enregistre en session pour être sur que le client est bien passé ici

        $random = random_bytes(10);
        $token = md5($random);
        $new_token = $token;

        $user->setVerifSubscription($new_token);
        $entity->persist($user);
        $entity->flush();

        $verif_token = $session->set('new_token', $new_token);

        return $this->redirectToRoute('success_page');
    
    }

    /**
     * //page affichant paiement réussi
     * @Route("/success", name="success_page")
     *
     */
    public function successPage(SessionInterface $session, UsersRepository $repo, EntityManagerInterface $entity, \Swift_Mailer $mailer  )
    {   
        
        //Vérifie si client a été enregistré et si il est bien passé par la redirection
        $verif_token = $session->get('new_token');        
        $successPayment = $repo->verifSubscriber($verif_token);

       //si token généré lors inscription absent, retour à tarif
        if(!$session->get('activationToken')) {

            return $this->redirectToRoute('tarif');

        }

        //si token succès en mémoire dans la table user, envoi mail activation du compte
        if ($successPayment) {

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
                );
                $mailer->send($message);
        }

        else {
            
            return $this->redirectToRoute('tarif');
        }

        return $this->render('subscription/success.html.twig');  
    }
}