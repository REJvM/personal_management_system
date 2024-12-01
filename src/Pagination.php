<?php

namespace App;

use Doctrine\ORM\EntityRepository;

class Pagination
{
    public const PER_PAGE = 20;
    public const PAGE_PARAMETER = 'page'; 

    public function getEntityForPage(EntityRepository $repository, int $page, array $criterias = []): mixed
    {
        $whereParameters = [];
        if(count($criterias) > 0) {
            foreach($criterias as $criteria => $value)
            {   
                $whereParameters[] = "u." . $criteria . " = '" . $value . "'";
            }    
        }

        $queryBuilder = $repository->createQueryBuilder('u');

        if(count($whereParameters) > 0) {
            foreach($whereParameters as $parameter)
            {
                $queryBuilder->where($parameter);  
            }
        }

        return $queryBuilder->setFirstResult((--$page) * self::PER_PAGE)            
                ->setMaxResults(self::PER_PAGE)
                ->getQuery()
                ->getResult();
    }

    public function getMaxPages(int $maxObjects): string
    {
        return $maxObjects / self::PER_PAGE;
    }

}
