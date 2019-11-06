<?php

namespace App\Tests;

use App\Services\Video;
use App\Twig\AppExtension;
use PHPUnit\Framework\TestCase;

class AppExtensionTest extends TestCase
{
    /**
     * Unit test for AppExtension class
     *
     * @return void
     */
    public function testCreateMediaHtmlFunction()
    {
        $appExtension = new AppExtension(new Video());

        // test for image
        $mediaFolder = '/gallery';
<<<<<<< HEAD
        $url = 'trick_media_6.jpg';
        $expectedResult = '<div class="media" data-media="/gallery/trick_media_6.jpg" data-type="image" style="background-image: url(/gallery/trick_media_6.jpg);"></div>';
        $this->assertSame($expectedResult, $appExtension->createMediaHtmlFunction($mediaFolder, $url));

        // test for video
        $mediaFolder = null;
        $youtubeUrl = 'https://www.youtube.com/embed/PHjpWo4edfw';
        $dailymotionUrl = 'https://www.dailymotion.com/embed/video/x5mmgov';
        $vimeoUrl = 'https://player.vimeo.com/video/40800548';
        $expectedYoutubeIframe = '<iframe class="media" src="' . $youtubeUrl . '" data-media="' . $youtubeUrl . '" data-type="youtube" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        $expectedDailymotionIframe = '<iframe class="media" data-media="' . $dailymotionUrl . '" frameborder="0" src="' . $dailymotionUrl . '" allowfullscreen allow="autoplay"></iframe>';
        $expectedVimeoIframe = '<iframe class="media" data-media="' . $vimeoUrl . '" src="' . $vimeoUrl . '" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
        $this->assertSame($expectedYoutubeIframe, $appExtension->createMediaHtmlFunction($mediaFolder, $youtubeUrl));
        $this->assertSame($expectedDailymotionIframe, $appExtension->createMediaHtmlFunction($mediaFolder, $dailymotionUrl));
        $this->assertSame($expectedVimeoIframe, $appExtension->createMediaHtmlFunction($mediaFolder, $vimeoUrl));
=======
        $url = 'image1_1.jpg';
        $expectedResult = '<div class="media" data-media="/gallery/image1_1.jpg" data-type="image" style="background-image: url(/gallery/image1_1.jpg);"></div>';
        $this->assertSame($expectedResult, $appExtension->createMediaHtmlFunction($mediaFolder, $url));

        // test for video
        $mediaFolder = '';
        $url = 'https://youtu.be/9BONcpuDcrc?list=PLFtT91yOzCD4ntUj6Ol7b9fBq5Uc12Wf7';
        $expectedResult = '<iframe class="media" src="http://www.youtube.com/embed/9BONcpuDcrc?list=PLFtT91yOzCD4ntUj6Ol7b9fBq5Uc12Wf7" data-media="http://www.youtube.com/embed/9BONcpuDcrc?list=PLFtT91yOzCD4ntUj6Ol7b9fBq5Uc12Wf7" data-type="youtube" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        $this->assertSame($expectedResult, $appExtension->createMediaHtmlFunction($mediaFolder, $url));
>>>>>>> 298483bc2ca9dac2dd5518824e2e3b89ceee8485
   }

}
