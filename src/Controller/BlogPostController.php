<?php

namespace App\Controller;

use DateTime;
use App\Entity\BlogPost;
use App\Form\BlogPostType;
use App\Form\Type\CkeditorType;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogPostController extends AbstractController
{
    #[Route('/dashboard/blog-posts', name: 'app_dashboard_blog_post')]
    public function index(
        BlogPostRepository $posts
    ): Response
    {
        return $this->render('dashboard/posts/index.html.twig', [
            'posts' => $posts->findAll()
        ]);
    }

    #[Route('/dashboard/blog-posts/create', name: 'app_dashboard_blog_post_create')]
    public function create(
        BlogPostRepository $posts,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $post = new BlogPost();
        $form = $this->createForm(BlogPostType::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $content = $form->get('content')->getData();
            $strippedContent = strip_tags($content, CkeditorType::ALLOWED_TAGS);
            
            $post->setTitle($form->get('title')->getData());
            $post->setContent($strippedContent);
            $post->setCreatedOn(new DateTime());
            $post->setCreatedBy($this->getUser());
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', sprintf('Blog post "%s" has been deleted.', $post->getTitle()));

            return $this->redirectToRoute('app_dashboard_blog_post', [
                'posts' => $posts->findAll()
            ]);
        }

        return $this->render('dashboard/posts/create.html.twig', [
            'form' => $form
        ]);
    }


    #[Route('/dashboard/blog-posts/{id}/edit', name: 'app_dashboard_blog_post_edit')]
    public function edit(
        BlogPost $post,
        Request $request,
        BlogPostRepository $posts,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(BlogPostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $content = $form->get('content')->getData();
            $strippedContent = strip_tags($content, CkeditorType::ALLOWED_TAGS);
            
            $post->setTitle($form->get('title')->getData());
            $post->setContent($strippedContent);
            $post->setModifiedOn(new DateTime());
            $post->setModifiedBy($this->getUser());
            
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', sprintf('Blog post "%s" has been edited.', $post->getTitle()));

            return $this->redirectToRoute('app_dashboard_blog_post', [
                'users' => $posts->findAll()
            ]);
        }

        return $this->render('dashboard/posts/edit.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }

    #[Route('/dashboard/blog-posts/{id}/delete', name: 'app_dashboard_blog_post_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        BlogPost $post,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', sprintf('Blog post "%s" has been deleted.', $post->getTitle()));

        return $this->redirect($request->headers->get('referer'));
    }
}
