<?php

namespace App\Twig\Extension;

use Twig\TwigFunction;
use Symfony\Component\Finder\Finder;
use Twig\Extension\AbstractExtension;

class BackgroundExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('random_background', [$this, 'getRandomBackground']),
        ];
    }

    public function getRandomBackground(): string
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__ .'/../../../assets/images/backgrounds')->name('*.jpg');

        $allImageNames = [];
        foreach ($finder as $file) {
            $allImageNames[] = $file->getFilename();
        }
        
        return (string) $allImageNames[array_rand($allImageNames)];
    }
}
