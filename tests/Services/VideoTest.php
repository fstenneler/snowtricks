<?php

namespace App\Tests;

use App\Services\Video;
use PHPUnit\Framework\TestCase;

class VideoTest extends TestCase
{

    /**
     * Unit test for isVideoCodeCorrect
     *
     * @return void
     */
    public function testIsVideoCodeCorrect()
    {   
        $videoCodeTrue = '<iframe frameborder="0" width="480" height="270" src="https://www.dailymotion.com/embed/video/x5mmgov" allowfullscreen allow="autoplay"></iframe>';        
        $videoCodeFalse = 'trick_media_6.jpg';
        $video = new Video();
        $this->assertSame(true, $video->isVideoCodeCorrect($videoCodeTrue));        
        $this->assertSame(false, $video->isVideoCodeCorrect($videoCodeFalse));        
    }

    /**
     * Unit test for isIframe
     *
     * @return void
     */
    public function testIsIframe()
    {   
        $videoCodeTrue = '<iframe frameborder="0" width="480" height="270" src="https://www.dailymotion.com/embed/video/x5mmgov" allowfullscreen allow="autoplay"></iframe>';        
        $videoCodeFalse = 'https://www.dailymotion.com/embed/video/x5mmgov';        
        $video = new Video();
        $this->assertSame(true, $video->isIframe($videoCodeTrue));        
        $this->assertSame(false, $video->isIframe($videoCodeFalse));        
    }

    /**
     * Unit test for isVideo
     *
     * @return void
     */
    public function testIsVideo()
    {   
        $urlTrue = 'https://www.dailymotion.com/embed/video/x5mmgov';
        $urlFalse = 'trick_media_6.jpg';
        $video = new Video();
        $this->assertSame(true, $video->isVideo($urlTrue));        
        $this->assertSame(false, $video->isVideo($urlFalse));
    }

    /**
     * Unit test for videoCodeToUrl
     *
     * @return void
     */
    public function testVideoCodeToUrl()
    {   
        $videoCode = '<iframe frameborder="0" width="480" height="270" src="https://www.dailymotion.com/embed/video/x5mmgov" allowfullscreen allow="autoplay"></iframe>';        
        $video = new Video();
        $this->assertSame('https://www.dailymotion.com/embed/video/x5mmgov', $video->videoCodeToUrl($videoCode));        
    }

    /**
     * Unit test for createIframe
     *
     * @return void
     */
    public function testCreateIframe()
    {   
        $video = new Video();
        $youtubeUrl = 'https://www.youtube.com/embed/PHjpWo4edfw';
        $dailymotionUrl = 'https://www.dailymotion.com/embed/video/x5mmgov';
        $vimeoUrl = 'https://player.vimeo.com/video/40800548';
        $expectedYoutubeIframe = '<iframe class="media" src="' . $youtubeUrl . '" data-media="' . $youtubeUrl . '" data-type="youtube" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        $expectedDailymotionIframe = '<iframe class="media" data-media="' . $dailymotionUrl . '" frameborder="0" src="' . $dailymotionUrl . '" allowfullscreen allow="autoplay"></iframe>';
        $expectedVimeoIframe = '<iframe class="media" data-media="' . $vimeoUrl . '" src="' . $vimeoUrl . '" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
        $this->assertSame($expectedYoutubeIframe, $video->createIframe($youtubeUrl));
        $this->assertSame($expectedDailymotionIframe, $video->createIframe($dailymotionUrl));
        $this->assertSame($expectedVimeoIframe, $video->createIframe($vimeoUrl));
    }

}
