<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('matches', [$this, 'matches']),
        ];
    }

    public function matches(string $haystack, string $needle): string
    {
        return str_contains($haystack, $needle);
    }
}