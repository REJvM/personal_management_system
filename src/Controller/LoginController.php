<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/', name: 'app_login')]
    public function index(
        AuthenticationUtils $utils
    ): Response {
        $lastUserName = $utils->getLastUsername();
        $error = $utils->getLastAuthenticationError();

        return $this->render('login/index.html.twig', [
            'lastUsername' => $lastUserName,
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout() {}
}
