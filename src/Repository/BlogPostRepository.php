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

    public function addIdToHeading(string &$htmlString)
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $htmlString);
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

        $htmlString = null;
        foreach ($dom->documentElement->childNodes as $n) {
            $htmlString .= $dom->saveHTML($n);
        }
    }


    public function addCodeLanguageToPre(string &$htmlString)
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $htmlString);
        libxml_use_internal_errors(false);

        $xpath = new DomXPath($dom);
        $pres = $xpath->query("//pre");

        /** @var DomElement $pre */
        foreach ($pres as $pre) {
            if (isset($pre->childNodes->item(0)->className)) {
                $codeClass = $pre->childNodes->item(0)->className;
                $codeLanguage = substr($codeClass, strlen('language-'));
                $pre->setAttribute('data-language', ucfirst($codeLanguage));
            }
        }

        $htmlString = null;
        foreach ($dom->documentElement->childNodes as $n) {
            $htmlString .= $dom->saveHTML($n);
        }
    }

    public function splitCodeIntoList(string &$htmlString) {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $htmlString);
        libxml_use_internal_errors(false);

        $xpath = new DomXPath($dom);
        $pres = $xpath->query("//pre");
         /** @var DomElement $pre */
         foreach ($pres as $pre) {
            $node = $pre->childNodes->item(0);
            if (isset($node->tagName) && $node->tagName === "code") {
                $linesInArray = explode(PHP_EOL, $node->firstChild->wholeText);
                $node->nodeValue = '';
                
                $lineNumber = 1;
                foreach($linesInArray as $line) {
                    $line = $dom->createElement('div', htmlspecialchars($line));
                    $line->setAttribute('data-number', $lineNumber);
                    $node->appendChild($line);
                    $lineNumber = ++$lineNumber; 
                }
            }
        }

        $htmlString = null;
        foreach ($dom->documentElement->childNodes as $n) {
            $htmlString .= $dom->saveHTML($n);
        }
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
