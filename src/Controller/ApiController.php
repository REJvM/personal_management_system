<?php

namespace App\Controller;

use App\Pagination;
use App\Entity\User;
use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiController extends AbstractController
{
    const VERSION_NUMBER = 1;
    const BASE_API_PATH = '/api/v' . self::VERSION_NUMBER;

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

    #[Route(self::BASE_API_PATH . '/openapi.json', name: 'app_api_openapi', format: 'json')]
    public function openapi(): Response
    {
        return $this->render('api/openapi.json');
    }

    #[Route(
        self::BASE_API_PATH . '/blog-posts',
        name: 'app_api_blog_post_list',
        format: 'json',
        methods: ['GET']
    )]
    public function blogPostList(
        Request $request,
        Pagination $pagination
    ): JsonResponse {
        $page = (int) $request->query->get('offset', 1);
        $limit = (int) $request->query->get('limit', 20);
        $category = (string) $request->query->get('category', null);

        $queryBuilder = $this->_posts->createQueryBuilder('p');
        if ($category !== null && in_array($category, BlogPost::AVAILABLE_CATEGORIES)) {
            $queryBuilder->andWhere('p.category = :val');
            $queryBuilder->setParameter('val', $category);
        }
        $blogPosts = $pagination->paginate($queryBuilder, $page, $limit);

        $items = [];
        foreach ($blogPosts['items'] as $blogPost) {
            $lastModifiedOn = $blogPost->getModifiedOn() !== null ? $blogPost->getModifiedOn() : $blogPost->getCreatedOn();
            $items[] = [
                'id' => $blogPost->getId(),
                'title' => $blogPost->getTitle(),
                'category' => $blogPost->getCategory(),
                'last_modified_on' => $lastModifiedOn,
            ];
        }

        return $this->json([
            'items' => $items,
            'pagination' => [
                'total' => $blogPosts['total'],
                'page' => $blogPosts['page'],
                'limit' => $blogPosts['limit'],
                'totalPages' => $blogPosts['totalPages'],
            ]
        ]);
    }

    #[Route(
        self::BASE_API_PATH . '/blog-posts/{id}',
        name: 'app_api_blog_post',
        format: 'json',
        methods: ['GET']
    )]
    public function blogPost(int $id): JsonResponse
    {
        $blogPost = $this->_posts->find($id);
        if ($blogPost instanceof BlogPost === false) {
            throw new NotFoundHttpException('No blog post found.');
        }

        $lastModifiedOn = $blogPost->getModifiedOn() !== null ? $blogPost->getModifiedOn() : $blogPost->getCreatedOn();

        $singlePostLinks = [];
        foreach ($blogPost->getLinks() as $link) {
            $linkInfo = [
                'url' => $link->getUrl(),
                'icon' => $link->getIcon()
            ];
            if ($link->getName()) {
                $linkInfo['name'] = $link->getName();
            }
            $singlePostLinks[] = $linkInfo;
        }

        $user = $blogPost->getCreatedBy();

        $parameters = [
            'title' => $blogPost->getTitle(),
            'content' => $blogPost->getContent(),
            'category' => $blogPost->getCategory(),
            'links' => $singlePostLinks,
            'last_modified_on' => $lastModifiedOn,
        ];

        if ($user instanceof User) {
            $userProfile = $user->getUserProfile();
            $parameters['user'] = [];
            $parameters['user']['name'] = $userProfile->getName() != null ? $userProfile->getName() : $user->getEmail();
            if ($userProfile->getPicture() !== null) {
                $parameters['user']['picture'] = $userProfile->getPicture()->getFileName();
            }
        }

        return $this->json($parameters);
    }
}
