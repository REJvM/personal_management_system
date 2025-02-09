<?php

namespace App\Repository;

use \DomXPath;
use \DOMDocument;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\BlogPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlogPost>
 */
class BlogPostRepository extends ServiceEntityRepository
{
    private $slugger;

    public function __construct(ManagerRegistry $registry, SluggerInterface $slugger)
    {
        $this->slugger = $slugger;

        parent::__construct($registry, BlogPost::class);
    }

    public function addIdToHeading(string $htmlString): string
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($htmlString);
        libxml_use_internal_errors(false);

        $xpath = new DomXPath($dom);
        $headers = $xpath->query("//h2");

        /** @var DomElement $header */
        foreach ($headers as $header) {
            $header->setAttribute(
                "id",
                $this->slugger->slug(
                    strtolower(
                        $header->nodeValue
                    )
                )
            );
        }

        $newContent = '';
        foreach ($dom->documentElement->childNodes as $n) {
            $newContent .= $dom->saveHTML($n);
        }
        return $newContent;
    }

    //    /**
    //     * @return BlogPost[] Returns an array of BlogPost objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?BlogPost
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
