<?php

namespace App\Controller;

use DateTime;
use App\Pagination;
use App\Entity\BlogPost;
use App\Form\BlogPostType;
use App\Form\Type\CkeditorType;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogPostController extends AbstractController
{
    private $_posts; 

    public function __construct(BlogPostRepository $posts) {
        $this->_posts = $posts;
    }

    #[Route('/dashboard/blog-posts', name: 'app_dashboard_blog_post')]
    public function index(
        Request $request,
        Pagination $pagination
    ): Response
    {
        $posts = $this->_posts;
        if($request->get('category')) {
            $posts = $posts->where("u.category = '" . $request->get('category') . "'");
        }

        $queryBuilder = $posts->createQueryBuilder('u');

        $page = $request->get('page', 1);
        $listedPosts = $pagination->paginate($queryBuilder, $page);

        return $this->render('dashboard/posts/index.html.twig', [
            'posts' => $listedPosts['items'],
            'totalPages' => $listedPosts['totalPages'],
            'categories' => BlogPost::CATEGORY_ICONS
        ]);
    }

    #[Route('/dashboard/blog-posts/create', name: 'app_dashboard_blog_post_create')]
    public function create(
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
            $post->setCategory($form->get('category')->getData());
            $post->setContent($strippedContent);
            $post->setCreatedOn(new DateTime());
            $post->setCreatedBy($this->getUser());
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', sprintf('Blog post "%s" has been deleted.', $post->getTitle()));

            return $this->redirectToRoute('app_dashboard_blog_post', [
                'posts' => $this->_posts->findAll()
            ]);
        }

        return $this->render('dashboard/posts/create.html.twig', [
            'form' => $form
        ]);
    }


    #[Route('/dashboard/blog-posts/edit', name: 'app_dashboard_blog_post_edit')]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        if($request->get('object_id') === null) {
            return $this->handleError('No blog post was found.');
        }

        $post = $this->_posts->find($request->get('object_id'));

        if($post === null) {
            return $this->handleError('No blog post was found.');
        }

        $form = $this->createForm(BlogPostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $content = $form->get('content')->getData();
            $strippedContent = strip_tags($content, CkeditorType::ALLOWED_TAGS);
            
            $post->setTitle($form->get('title')->getData());
            $post->setCategory($form->get('category')->getData());
            $post->setContent($strippedContent);
            $post->setModifiedOn(new DateTime());
            $post->setModifiedBy($this->getUser());
            
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', sprintf('Blog post "%s" has been edited.', $post->getTitle()));

            return $this->redirectToRoute('app_dashboard_blog_post', [
                'users' => $this->_posts->findAll()
            ]);
        }

        return $this->render('dashboard/posts/edit.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }

    #[Route('/dashboard/blog-posts/delete', name: 'app_dashboard_blog_post_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        if($request->get('object_id') == null) {
            return  $this->handleError('No blog post was found.');
        }

        $post = $this->_posts->find($request->get('object_id'));

        if($post === null) {
            return  $this->handleError('No blog post was found.');
        }

        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', sprintf('Blog post "%s" has been deleted.', $post->getTitle()));

        return $this->redirect($request->headers->get('referer'));
    }

    protected function handleError(string $message): RedirectResponse 
    {
        $this->addFlash('error', $message);
        
        return $this->redirectToRoute('app_dashboard_blog_post', [
            'users' => $this->_posts->findAll()
        ]);
    }
}
