<?php

namespace App\Twig;

use Twig\TwigFunction;
use App\Services\Video;
use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{
    private $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('createMediaHtml', [$this, 'createMediaHtmlFunction']),
        ];
    }

    /**
     * Create iframe or video Html code in twig templates
     *
     * @param string $mediaFolder
     * @param string $url
     * @return string
     */
    public function createMediaHtmlFunction($mediaFolder, $url)
    {
        if($this->video->isvideo($url)) {
            return $this->video->createIframe($url);
        }
        return '<div class="media" data-media="' . htmlspecialchars($mediaFolder) . '/' . $url . '" data-type="image" style="background-image: url(' . htmlspecialchars($mediaFolder) . '/' . htmlspecialchars($url) .');"></div>';        
    }
}
