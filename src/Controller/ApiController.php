<?php

namespace App\Controller;

use App\Pagination;
use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiController extends AbstractController
{
    const VERSION_NUMBER = 1;
    const BASE_API_PATH = '/api/v'.self::VERSION_NUMBER;

    private $_posts;

    public function __construct(
        BlogPostRepository $posts
    ) {
        $this->_posts = $posts;
    }

    #[Route(self::BASE_API_PATH, name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/swagger.html.twig');
    }

    #[Route(self::BASE_API_PATH.'/openapi.json', name: 'app_api_openapi', format:'json')]
    public function openapi(): Response
    {
        return $this->render('api/openapi.json');
    }
    
    #[Route(
        self::BASE_API_PATH.'/blog-posts', 
        name: 'app_api_blog_post_list', 
        format:'json',
        methods:['GET']
    )]
    public function blogPostList(Request $request): JsonResponse
    {
        $page = (int) $request->query->get('offset', 1);
        $limit = (int) $request->query->get('limit', 10);
        $offset = ($page - 1) * $limit;

        $blogPosts = $this->_posts->createQueryBuilder('p')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $totalBlogPosts = $this->_posts->createQueryBuilder('p')
            ->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $items = [];
        foreach ($blogPosts as $blogPost) {
            
            $lastModifiedOn = $blogPost->getModifiedOn() !== null ? $blogPost->getModifiedOn() : $blogPost->getCreatedOn();
            $items[] = [
                'id' => $blogPost->getId(),
                'title' => $blogPost->getTitle(),
                'category' => $blogPost->getCategory(),
                'last_modified_on' => $lastModifiedOn,
            ];
        }

        $totalPages = ceil($totalBlogPosts / $limit);

        return $this->json([
            'items' => $items,
            'pagination' => [
                'total' => (int) $totalBlogPosts,
                'page' => (int) $page,
                'limit' => (int) $limit,
                'totalPages' => (int) $totalPages,
            ]
        ]);
    }

    #[Route(
        self::BASE_API_PATH.'/blog-posts/{id}', 
        name: 'app_api_blog_post', 
        format:'json',
        methods:['GET']
    )]
    public function blogPost(int $id): JsonResponse
    {
        $blogPost = $this->_posts->find($id);
        if ($blogPost instanceof BlogPost === false) {
            throw new NotFoundHttpException('No blog post found.');
        }

        $lastModifiedOn = $blogPost->getModifiedOn() !== null ? $blogPost->getModifiedOn() : $blogPost->getCreatedOn();

        $postResponse = [
            'title' => $blogPost->getTitle(),
            'content' => $blogPost->getContent(),
            'category' => $blogPost->getCategory(),
            'last_modified_on' => $lastModifiedOn
        ];

        return $this->json($postResponse);
    }
}
