<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\Customer;
use App\Entity\Users;
use Stripe\SetupIntent;
use App\Form\InscriptionType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SubscriptionController extends AbstractController
{
    
    
    /**
     * @Route("/tarifs", name="tarifs")
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
            // si le tarif transmis est différent des tarifs starter ou premium, on redirige
            if($plan != 'starter' && $plan != 'premium') {


                return $this->redirectToRoute('tarifs');


            }
        }
        
        // on génère une nouvelle instance de user
        $user = new Users();
        
        // On génère le formulaire d'inscription
        $form = $this->createForm(InscriptionType::class, $user);
        $form->handleRequest($request);

        // Si le formulaire d'inscription est valide
        if($form->isSubmitted() && $form->isValid()) {

            // on crypte puis enregistre le mot de passe de l'user
            $passwordCrypte = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($passwordCrypte);
            $user->setConfirmPassword($passwordCrypte);
            
            // on ajoute un token activation
            $user->setActivationToken(md5(uniqid()));
            
            // on lui attribue un rôle awaiting en attendant que son compte soit validé
            $user->setRoles('ROLE_AWAITING');

            // On indique la date d'inscription
            $date = new \Datetime();
            $user->setCreatedAt($date);
            
            // On indique le plan souscrit et que l'abonnement n'est pas encore actif
            $user->setPlan($plan);
            $user->setActivated(false);

            // on enregistre toutes les données de l'inscription dans la table user de la bdd
            $entity->persist($user);
            $entity->flush();   

            // on enregistre le token d'activation et l'email en session car on en aura besoin plus tard
            $userToken = $session->set('activationToken', $user->getActivationToken());
            $userMail = $session->set('email', $user->getEmail()); 
            
            // on redirige vers le formulaire de paiement
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
        // ON récupère les identifiants de l'user à partir de la variable user
        $name = $user->getUserName(); 
        $email = $user->getEmail();
        $userId = $user->getId();
        $plan = $user->getPlan();
        
        // si l'user qui atterit sur la page de paiement s'est déjà inscrit
        if($user->getVerifsubscription() != null) {
            
            // on le redirige sur la page accueil
            return $this->redirectToRoute('home');
        }

        // jeton d'accès à l'api de stripe
        Stripe::setApiKey('sk_test_51HpdbCLfEkLbwHD1453jzn7Y69TdfWFJ9zzpYWSlL6Y7w3RgWgTOs7MQN91BzrP11C5jUquQFi1b8LK4GyIs10Gu00jH3iKTqe');

        //On crée un nouveau client sur stripe à partir du nom et email
        $customer = Customer::create(["name" => $name, "email" => $email]);

        //On créé un nouveau setupintent sur stripe pour ce client afin de pouvoir le débiter plus tard.
        $intent = SetupIntent::create([
            'customer' => $customer->id
          ]);

        //On enregistre en base de données le customer id stripe du client afin de pouvoir l'utiliser plus tard
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

        // On enregistre ce token en base de données 
        $user->setVerifSubscription($new_token);
        $entity->persist($user);
        $entity->flush();

        // on enregistre le token dans une session aussi pour l'utiliser plus atard
        $verif_token = $session->set('new_token', $new_token);

        // on redirige vers la page de paiement réussi 
        return $this->redirectToRoute('success_page');
    
    }

    /**
     * //page affichant paiement réussi
     * @Route("/success", name="success_page")
     *
     */
    public function successPage(SessionInterface $session, UsersRepository $repo, \Swift_Mailer $mailer  )
    {   
        
        //Vérifie si client qui atterit sur cette page a été enregistré et si il est bien passé par la redirection
        $verif_token = $session->get('new_token');        
        $successPayment = $repo->verifSubscriber($verif_token);

       // On vérifie si token généré lors inscription est absent, le cas échéant retour à tarif
        if(!$session->get('activationToken')) {

            return $this->redirectToRoute('tarif');

        }

        //si token générée suite paiement réussi en mémoire dans la table user, envoi mail activation du compte
        if ($successPayment) {

            //envoie mail activation compte avec token activation compte et email user obtenue lors de l'inscription
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
                // on envoie le mail
                $mailer->send($message);
        }

        // si le token de paiement réussi est absent, cela signife qu'aucun paiment n'a été pris, on redirige 
        else {
            
            // on redirige vers la page des tarifs
            return $this->redirectToRoute('tarifs');
        }

        return $this->render('subscription/success.html.twig');  
    }
}