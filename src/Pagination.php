<?php

namespace App;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;


class Pagination
{
    public const DEFUALT_LIMIT = 20; 
    public const PAGE_PARAMETER = 'page'; 

    public function paginate(
        QueryBuilder $queryBuilder,
        int $page = 1, 
        int $limit = self::DEFUALT_LIMIT
    ): array {
        $queryBuilder
            ->setFirstResult((--$page) * $limit)            
            ->setMaxResults($limit);

        $paginator = new Paginator($queryBuilder);
        $totalResults = count($paginator);

        return [
            'items' => iterator_to_array($paginator),
            'total' => (int) $totalResults,
            'page' => (int) $page,
            'limit' => (int) $limit,
            'totalPages' => (int) ceil($totalResults / $limit),
        ];
    }
}
