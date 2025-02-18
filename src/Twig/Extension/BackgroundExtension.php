<?php

namespace App\Twig\Extension;

use Twig\TwigFunction;
use Symfony\Component\Finder\Finder;
use Twig\Extension\AbstractExtension;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BackgroundExtension extends AbstractExtension
{
    private $_projectDir;

    public function __construct(ParameterBagInterface $params)
    {
        $this->_projectDir = $params->get('kernel.project_dir');
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('random_background', [$this, 'getRandomBackground']),
        ];
    }

    public function getRandomBackground(string $filePath): string
    {
        $finder = new Finder();
        $finder->files()->in($this->_projectDir . '/public/' . $filePath)->name('*.jpg');

        $allImageNames = [];
        foreach ($finder as $file) {
            $allImageNames[] = $file->getFilename();
        }

        return (string) $filePath . $allImageNames[array_rand($allImageNames)];
    }
}
