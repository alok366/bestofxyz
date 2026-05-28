<?php

namespace Framework\TwigExtensions;

use \Twig\Extension\AbstractExtension;
use \Twig\TwigFilter;

final class MinifyExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('minify', [$this, 'minify']),
        ];
    }

    public function minify($string)
    {
        // Remove newlines, tabs, and extra spaces
        return preg_replace('/\s+/', ' ', $string);
    }    

}