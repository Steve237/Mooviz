<?php

namespace App\Controller;

use App\Entity\Users;
use Stripe\StripeClient;
use App\Form\AdminUserType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin_space/{plan}", name="admin_space")
     */
    public function admin_space($plan, UsersRepository $repoUser, Request $request, PaginatorInterface $paginator): Response
    {   
        
        $users = $paginator->paginate(
            $repoUser->findAllWithPagination(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );
        

        if ($plan != 'starter' && $plan != 'premium' && $plan != 'free') {

            return $this->redirectToRoute('homepage');
        }
          
        return $this->render('admin/adminspace.html.twig', [
            
            'users' => $users,
            'plan' => $plan,
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

            return $this->redirectToRoute('homepage');


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
        $stripe = new \Stripe\StripeClient(
            'sk_test_51HpdbCLfEkLbwHD1453jzn7Y69TdfWFJ9zzpYWSlL6Y7w3RgWgTOs7MQN91BzrP11C5jUquQFi1b8LK4GyIs10Gu00jH3iKTqe'
          );
          
          $stripe->customers->delete(
            
            $user->getCustomerid(),
            []
          );

        $entity->remove($user);
        $entity->flush();
        
        $this->addflash("success", "suppression du membre réussi");
        return $this->redirectToRoute('admin_space');
        
    }

}