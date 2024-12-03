<?php

namespace App\Controller;

use App\Pagination;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private $_users;

    public function __construct(
        UserRepository $users
    ) {
        $this->_users = $users;
    }

    #[Route('/dashboard/users', name: 'app_dashboard_user')]
    public function users(
        Request $request,
        Pagination $pagination
    ): Response
    {
        $page = $request->get('page') ?? 1;
        $listedUsers = $pagination->getEntityForPage($this->_users, $page);

        return $this->render('dashboard/users/index.html.twig', [
            'users' => $listedUsers,            
            'maxPages' => $pagination->getMaxPages($this->_users->count())
        ]);
    }

    #[Route('/dashboard/users/edit', name: 'app_dashboard_user_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher, 
    ): Response {
    
        if($request->get('object_id') === null) {
            return $this->handleError('No user was found.');
        }

        $user = $this->_users->find($request->get('object_id'));
        if($user === null) {
            return $this->handleError('No user was found.');
        }

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

            $this->addFlash('success', sprintf('User "%s" has been edited.', $user->getEmail()));

            return $this->redirectToRoute('app_dashboard_user', [
                'users' => $this->_users->findAll()
            ]);
        }

        return $this->render('dashboard/users/edit.html.twig', [
            'form' => $form,
            'user' => $user
        ]);
    }

    
    #[Route('/dashboard/users/delete', name: 'app_dashboard_user_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        if($request->get('object_id') === null) {
            return $this->handleError('No user was found.');
        }
        
        $user = $this->_users->find($request->get('object_id'));

        if($user === null) {
            return $this->handleError('No user was found.');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', sprintf('User "%s" has been deleted.', $user->getEmail()));

        return $this->redirect($request->headers->get('referer'));
    }

    protected function handleError(string $message): RedirectResponse 
    {
        $this->addFlash('error', $message);
        
        return $this->redirectToRoute('app_dashboard_user', [
            'users' => $this->_users->findAll()
        ]);
    }
}
