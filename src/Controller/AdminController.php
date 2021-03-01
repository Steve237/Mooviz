<?php

namespace App\Controller;

use DateTime;
use Stripe\Stripe;
use App\Entity\Users;
use App\Entity\Webhook;
use Stripe\StripeClient;
use App\Form\AdminUserType;
use App\Entity\Videobackground;
use App\Entity\Videos;
use App\Form\VideoBackgroundType;
use App\Repository\UsersRepository;
use App\Repository\VideosRepository;
use App\Repository\WebhookRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SubscriptionRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\VideobackgroundRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdminController extends AbstractController
{

    /**
     * @Route("/admin/users_list", name="users_list")
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
    * @Route("/admin/update_user/{id}", name="update_user")
    */
    public function updateUser(Users $user, Request $request, EntityManagerInterface $entity, UsersRepository $repoUser): Response
    {   

        if ($request->isMethod('POST')) {

           $email = $request->request->get('email');
           $username = $request->request->get('username');
           $roles = $request->request->get('roles');

           $userExist = $repoUser->findUser($username);
           $emailExist = $repoUser->findEmail($email);

            if($userExist){

                $this->addFlash('user_exist', 'Ce nom est déjà utilisé.');
                return $this->redirectToRoute('update_user', ['id' => $user->getId()]);
            }

            
            if($emailExist){

                $this->addFlash('email_exist', 'Cet email est déjà utilisé.');
                return $this->redirectToRoute('update_user', ['id' => $user->getId()]);

            }



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
     * @Route("/admin/delete_user/{id}", name="delete_user")
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
    * @Route("/admin/notifications_list", name="notifications_list")
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
    * @Route("/admin/delete_notification/{id}", name="delete_notification")
    */
    public function deleteNotification(Webhook $webhook, EntityManagerInterface $entity, WebhookRepository $repo)
    {   
        $entity->remove($webhook);
        $entity->flush();

        $this->addflash('success', 'La notification a été supprimée.');
        return $this->redirectToRoute('notifications_list');
        
    }

     /**
    * @Route("/admin/delete_notifications", name="delete_notifications")
    */
    public function deleteNotifications(EntityManagerInterface $entity, WebhookRepository $repo)
    {   
        $repo->deleteAllWebhook();

        $this->addflash('success', 'Toutes les notifications ont été supprimées');
        return $this->redirectToRoute('notifications_list');
        
    }


    /**
    * @Route("/admin/block_user/{id}", name="block_user")
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
    * @Route("/admin/deblock_user/{id}", name="deblock_user")
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
    * @Route("/admin/webhook", name="webhook")
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
    * @Route("/admin/payment_intent_success", name="payment_intent_success")
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
    * @Route("/admin/update_payment_status/{customer}", name="update_payment_status")
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
    * @Route("/admin/payment_intent_failed", name="payment_intent_failed")
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
    * @Route("/admin/failed_payment_status/{customer}", name="failed_payment_status")
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


    /** 
    * //donne accès à la liste des backgrounds videos
    * @Route("/admin/videos_background", name="videos_background")
    */
    public function videoBackgroundList(VideobackgroundRepository $videoRepo) 
    {
        $videos = $videoRepo->findAll();

        return $this->render('admin/videos_background.html.twig', 
        [
            "videos" => $videos

        ]);
       
    }

    /**
    * @Route("/admin/update_video_background/{id}", name="update_video_background")
    */
    public function updateVideoBackground(Videobackground $video, EntityManagerInterface $entity, Request $request) 
    {   
        $videoBackgroundForm = $this->createForm(VideoBackgroundType::class, $video);
        $videoBackgroundForm->handleRequest($request);

        if($request->isXmlHttpRequest()) {
        
            if($videoBackgroundForm->isSubmitted() && $videoBackgroundForm->isValid()) {
                
            //upload de la vidéo
            $videoFile = $video->getVideoLink();
            $fileVideo = md5(uniqid()).'.'.$videoFile->guessExtension();
            $videoFile->move($this->getParameter('video_directory'), $fileVideo);

            $video->setVideoLink('/uploads/'.$fileVideo);
            $entity->persist($video);
            $entity->flush();
            
            return new JsonResponse('ok');
        }
            return new JsonResponse('err');
        }
        
        return $this->render('admin/update_video_background.html.twig', 
        [   
            "videoBackgroundForm" => $videoBackgroundForm->createView(),
            "video" => $video

        ]);
    }

    /**
    * @Route("/admin/success_update_video_background", name="success_update_video_background")
    */
    public function successUpdateVideoBackground() 
    {   
        $this->addFlash('success_upload', 'votre vidéo a été modifié avec succès');
        
        return $this->redirectToRoute('videos_background');
    }


    /**
    * @Route("/admin/adminspace_videos", name="adminspace_videos")
    */
    public function adminspaceVideos(VideosRepository $videoRepo, PaginatorInterface $paginator, Request $request) 
    {       
        
            $videos = $paginator->paginate(
            $videoRepo->findAllWithPagination(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            21 /*limit per page*/
        );
        
        return $this->render('admin/admin_videos.html.twig', 
        [
            "videos" => $videos

        ]);

    }

    /**
     * @Route("/admin/admin_delete_video/{id}", name="admin_delete_video")
     * //permet à l'user de supprimer les vidéos qu'il a ajouté
     */
    public function deleteVideoInAdminspace(Videos $video, EntityManagerInterface $entityManager, \Swift_Mailer $mailer) {

        $videotitle = $video->getVideotitle();
        $username = $video->getusername();
        $email = $username->getEmail();

         // On crée le message
         $message = (new \Swift_Message('Suppression de vidéo'))
         // On attribue l'expéditeur
         ->setFrom('essonoadou@gmail.com')
         // On attribue le destinataire
         ->setTo($email)
         // On crée le texte avec la vue
         ->setBody($this->renderView('email/delete_video_message.html.twig', ["videotitle" => $videotitle]),'text/html')
     ;
        $mailer->send($message);
        
        $entityManager->remove($video);
        $entityManager->flush();

        $this->addFlash('admin_delete_video', 'Cette vidéo a été supprimé avec succès');
        
        return $this->redirectToRoute('adminspace_videos');
    }

    /**
     * //retourne le formulaire de recherche
    */
    public function searchUser() {
    
        $form = $this->createFormBuilder(null)
            
        ->setAction($this->generateUrl("user_search"))
            ->add('query', TextType::class)

            ->getForm();
        
        
        return $this->render('admin/user_search.html.twig', [


            'form' => $form->createView()

        ]);

    }

    /**
     * //traite la requête envoyé dans le formulaire de recherche.
     * @Route("/admin/user_search", name="user_search")
     *
     */
    public function userSearch(Request $request, UsersRepository $repository, PaginatorInterface $paginator) {

        $query = $request->request->get('form')['query'];
        
        if ($query) {

            $users = $paginator->paginate(
            $repository->userSearch($query),
            $request->query->getInt('page', 1),
            20 /*limit per page*/
        );

            if ($repository->userSearch($query) == null) {

                return $this->render('admin/no_user.html.twig');
            }

            else {


                return $this->render('admin/user_search_result.html.twig', [

                    'users' => $users
        
                ]);

            }

        }
    }
}