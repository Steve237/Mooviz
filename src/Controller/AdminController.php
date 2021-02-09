<?php

namespace App\Controller;

use DateTime;
use Stripe\Stripe;
use App\Entity\Users;
use App\Entity\Webhook;
use Stripe\StripeClient;
use App\Form\AdminUserType;
use App\Repository\UsersRepository;
use App\Repository\VideosRepository;
use App\Repository\WebhookRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SubscriptionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{

    /**
     * @Route("/users_list", name="users_list")
     */
    public function admin_space(UsersRepository $repoUser, Request $request, PaginatorInterface $paginator): Response
    {   
        
        $users = $paginator->paginate(
            $repoUser->findAllWithPagination(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );
        
        return $this->render('admin/adminspace.html.twig', [
            
            'users' => $users,
        ]);
    }

    /**
    * @Route("/update_user/{id}", name="update_user")
    */
    public function updateUser(Users $user, Request $request, EntityManagerInterface $entity): Response
    {   

        if ($request->isMethod('POST')) {

           $email = $request->request->get('email');
           $username = $request->request->get('username');
           $roles = $request->request->get('roles');

           $user->setEmail($email);
           $user->setUsername($username);
           $user->setRoles($roles);
           $entity->persist($user);
           $entity->flush();

           $stripe = new \Stripe\StripeClient(
            'sk_test_51HpdbCLfEkLbwHD1453jzn7Y69TdfWFJ9zzpYWSlL6Y7w3RgWgTOs7MQN91BzrP11C5jUquQFi1b8LK4GyIs10Gu00jH3iKTqe'
          );
          $stripe->customers->update(
            $user->getCustomerid(),
            ["name" => $username, "email" => $email]
          );

            $date = new \Datetime();

            $notification = new Webhook();
            $notification->setType('modification');
            $notification->setContent('modification des identifiants');  
            $notification->setCreatedAt($date);
            $notification->setUsername($username);
            $entity->persist($notification);
            $entity->flush();  

            return $this->redirectToRoute('users_list');
        }

        return $this->render('admin/updateuser.html.twig', [
            
            'user' => $user
        ]);
    }

    /**
     * @Route("/delete_user/{id}", name="delete_user")
     */
    public function deleteUser(Users $user, EntityManagerInterface $entity)
    {   
        $date = new \Datetime();
        $username = $user->getUsername();

        $notification = new Webhook();
        $notification->setType('suppression');
        $notification->setContent('suppression de compte');  
        $notification->setCreatedAt($date);
        $notification->setUsername($username);
        $entity->persist($notification);
        $entity->flush();  
        
        $stripe = new \Stripe\StripeClient(
            'sk_test_51HpdbCLfEkLbwHD1453jzn7Y69TdfWFJ9zzpYWSlL6Y7w3RgWgTOs7MQN91BzrP11C5jUquQFi1b8LK4GyIs10Gu00jH3iKTqe'
          );
          
          $stripe->customers->delete(
            
            $user->getCustomerid(),
            []
          );

        $entity->remove($user);
        $entity->flush();

        $this->addflash("success", "Suppression du membre réussie.");
        return $this->redirectToRoute('users_list');
        
    }


    /**
    * @Route("/notifications_list", name="notifications_list")
    */
    public function AdminNotificationList(EntityManagerInterface $entity, WebhookRepository $repo, PaginatorInterface $paginator, Request $request)
    {   

        $notifications = $paginator->paginate(
            $repo->findAllWithPagination(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );

        return $this->render('admin/admin_notifications.html.twig', ['notifications' => $notifications]);
        
    }


    /**
    * @Route("/delete_notification/{id}", name="delete_notification")
    */
    public function deleteNotification(Webhook $webhook, EntityManagerInterface $entity, WebhookRepository $repo)
    {   
        $entity->remove($webhook);
        $entity->flush();

        $this->addflash('success', 'La notification a été supprimée.');
        return $this->redirectToRoute('notifications_list');
        
    }

     /**
    * @Route("/delete_notifications", name="delete_notifications")
    */
    public function deleteNotifications(EntityManagerInterface $entity, WebhookRepository $repo)
    {   
        $repo->deleteAllWebhook();

        $this->addflash('success', 'Toutes les notifications ont été supprimées');
        return $this->redirectToRoute('notifications_list');
        
    }


    /**
    * @Route("/block_user/{id}", name="block_user")
    */
    public function blockUser(Users $user, EntityManagerInterface $entity)
    {   
        $user->setRoles('ROLE_BLOCKED');
        $entity->persist($user);
        $entity->flush();

        $this->addflash('success', 'Le blocage a été réalisé avec succès.');
        return $this->redirectToRoute('users_list');
        
    }


    /**
    * @Route("/deblock_user/{id}", name="deblock_user")
    */
    public function deblockUser(Users $user, EntityManagerInterface $entity)
    {   
        $user->setRoles('ROLE_USER');
        $entity->persist($user);
        $entity->flush();

        $this->addflash('success', 'Le déblocage a été réalisé avec succès.');
        return $this->redirectToRoute('users_list');
        
    }

    
    /**
    * @Route("/webhook", name="webhook")
    */
    public function Webhook() {


        Stripe::setApiKey('sk_test_51HpdbCLfEkLbwHD1453jzn7Y69TdfWFJ9zzpYWSlL6Y7w3RgWgTOs7MQN91BzrP11C5jUquQFi1b8LK4GyIs10Gu00jH3iKTqe');


        $payload = @file_get_contents('php://input');
        $event = null;
        try {
        $event = \Stripe\Event::constructFrom(
            json_decode($payload, true)
        );
        } catch(\UnexpectedValueException $e) {
        // Invalid payload
        return new Response(Response::HTTP_OK);
        exit();
        }
        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                return $this->handlePaymentIntentSucceeded($paymentIntent);
                
            break;
            case 'invoice.payment_failed':
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentMethod
                // Then define and call a method to handle the successful attachment of a PaymentMethod.
                return $this->invoicePaymentFailed($paymentIntent);
                break;
            default:
                // Unexpected event type
                return $this->redirectToRoute('home');
        }
        
        return new Response(Response::HTTP_OK);
}


    /**
     * //Envoi notification pour indiquer paiement réussie
    * @Route("/payment_intent_success", name="payment_intent_success")
    */
    public function handlePaymentIntentSucceeded($paymentIntent) {

        $customer = $paymentIntent->customer;
        $user = $this->getDoctrine()->getRepository(Users::class)
        ->getCustomer($customer);

        $username = $user['username'];

        $date = new \Datetime();
        $entity = $this->getDoctrine()->getManager();

        $notification = new Webhook();
        $notification->setType('paiement réussi');
        $notification->setContent('mensualité payée');  
        $notification->setCreatedAt($date);
        $notification->setUsername($username);
        $entity->persist($notification);
        $entity->flush();  

        return $this->redirectToRoute('update_payment_status', ['customer' => $customer]);

    }

     /**
    * Met à jour le statut de paiement de l'utilisateur pour indiquer que son abonnement est à jour
    * @Route("/update_payment_status/{customer}", name="update_payment_status")
    */
    public function updatePaymentStatus($customer, UsersRepository $repoUser, EntityManagerInterface $entity) 
    {
        $user = $repoUser->findCustomer($customer);

            $user->setPayed(true);
            $entity->persist($user);
            $entity->flush();

            return new Response(Response::HTTP_OK);
    }

    /**
     * //Envoi notification pour indiquer paiement réussie
    * @Route("/payment_intent_failed", name="payment_intent_failed")
    */
    public function invoicePaymentFailed($paymentIntent) {

        $customer = $paymentIntent->customer;
        $user = $this->getDoctrine()->getRepository(Users::class)
        ->getCustomer($customer);

        $username = $user['username'];

        $date = new \Datetime();
        $entity = $this->getDoctrine()->getManager();

        $notification = new Webhook();
        $notification->setType('echec de paiement');
        $notification->setContent('mensualité impayée');  
        $notification->setCreatedAt($date);
        $notification->setUsername($username);
        $entity->persist($notification);
        $entity->flush();  

        return $this->redirectToRoute('failed_payment_status', ['customer' => $customer]);

    }

    /**
    * Met à jour le statut de paiement de l'utilisateur pour indiquer que son abonnement a échoué.
    * @Route("/failed_payment_status/{customer}", name="failed_payment_status")
    */
    public function paymentFailed($customer, UsersRepository $repoUser, EntityManagerInterface $entity) 
    {
        $user = $repoUser->findCustomer($customer);
        
            $user->setPayed(false);
            $user->setRoles('ROLE_BLOCKED');
            $entity->persist($user);
            $entity->flush();

            return new Response(Response::HTTP_OK);
    }
}