<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('createMediaHtml', [$this, 'createMediaHtmlFunction']),
        ];
    }

    public function createMediaHtmlFunction($mediaFolder, $url)
    {
        if(preg_match("#youtu#", $url)) {
            //return '<iframe class="media" src="' . $url . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
            return null;
        }
        //return '<img src="' . htmlspecialchars($mediaFolder) . '/' . htmlspecialchars($url) .'" alt="' . htmlspecialchars($alt) . '" />';
        return '<div class="media" style="background-image: url(' . htmlspecialchars($mediaFolder) . '/' . htmlspecialchars($url) .');"></div>';
        
    }
}
