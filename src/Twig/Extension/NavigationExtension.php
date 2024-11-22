<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NavigationExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('active_route', [$this, 'getActiveRoute']),
        ];
    }
    
    public function getActiveRoute(string $value, string $route): string
    {
        // check if route is in the first part of the value.
        return $value === substr($route, 0, strlen($value)) ? 'active' : '';
    }
}
