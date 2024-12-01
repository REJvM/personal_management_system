<?php

namespace App\Twig\Extension;

use App\Pagination;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class PaginationExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('pagination_path', [$this, 'getPaginationPath']),
            new TwigFunction('active_page', [$this, 'getActivePage']),
        ];
    }
    
    public function getActivePage(string|null $queryString, int $pageNumber): string
    {
        $queryParameters = [];
        $active = $pageNumber === 1;
        parse_str($queryString, $queryParameters);
        if($queryString !== null && array_key_exists(Pagination::PAGE_PARAMETER, $queryParameters)) {
            $active = (int) $queryParameters[Pagination::PAGE_PARAMETER] === $pageNumber;
        }
        return $active ? 'active' : '';
    }

    public function getPaginationPath(string $pathinfo, string|null $queryString, int $pagenumber): string
    {
        $query = '';
        if($queryString !== null) {
            parse_str($queryString, $queryParameters);
        }
        $queryParameters[Pagination::PAGE_PARAMETER] = $pagenumber;
        $query = '?' . http_build_query($queryParameters); 
        return $pathinfo . $query;
    }
}
