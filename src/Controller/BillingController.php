<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\BillingPortal\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BillingController extends AbstractController
{
    /**
     * @Route("/main/create-customer-portal-session", name="billing")
     * // permet à user d'accéder à son portail facturation de stripe
     */
    public function customerPortal()
    {
        Stripe::setApiKey('sk_test_51HpdbCLfEkLbwHD1453jzn7Y69TdfWFJ9zzpYWSlL6Y7w3RgWgTOs7MQN91BzrP11C5jUquQFi1b8LK4GyIs10Gu00jH3iKTqe');

        // Authenticate your user.
        $user = $this->getUser();
        
        $session = Session::create([
        'customer' => $user->getCustomerId(),
        'return_url' => 'http://127.0.0.1:8000/main/videolist'
        ]);

        header("Location: " . $session->url);
        exit();
    }
}
