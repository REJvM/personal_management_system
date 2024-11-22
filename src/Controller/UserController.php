<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/dashboard/users', name: 'app_dashboard_user')]
    public function users(
        UserRepository $users,
    ): Response
    {
        return $this->render('dashboard/users/users.html.twig', [
            'users' => $users->findAll()
        ]);
    }

    #[Route('/dashboard/users/{id}/edit', name: 'app_dashboard_user_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        User $user,
        Request $request,
        UserRepository $users,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher, 
    ): Response {
    
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $roles = $form->get('roles')->getData();

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            
            $user->setRoles($roles);
            
            if($plainPassword) {
                // encode the plain password
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            }
            
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', sprintf('User %s has been edited.', $user->getEmail()));

            return $this->redirectToRoute('app_dashboard_user', [
                'users' => $users->findAll()
            ]);
        }

        return $this->render('dashboard/users/edit.html.twig', [
            'form' => $form,
            'user' => $user
        ]);
    }

    
    #[Route('/dashboard/users/{id}/delete', name: 'app_dashboard_user_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', sprintf('User %s has been deleted.', $user->getEmail()));

        return $this->redirect($request->headers->get('referer'));
    }
}
