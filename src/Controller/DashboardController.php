<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
        ]);
    }

    #[Route('/dashboard/users', name: 'app_dashboard_users')]
    public function users(
        UserRepository $users,
    ): Response
    {
        return $this->render('dashboard/users.html.twig', [
            'users' => $users->findAll()
        ]);
    }    
}
