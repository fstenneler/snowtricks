<?php

namespace App\Tests;

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
        $appExtension = new AppExtension();

        // test for image
        $mediaFolder = '/gallery';
        $url = 'image1_1.jpg';
        $expectedResult = '<div class="media" data-media="/gallery/image1_1.jpg" data-type="image" style="background-image: url(/gallery/image1_1.jpg);"></div>';
        $this->assertSame($expectedResult, $appExtension->createMediaHtmlFunction($mediaFolder, $url));

        // test for video
        $mediaFolder = '';
        $url = 'https://youtu.be/9BONcpuDcrc?list=PLFtT91yOzCD4ntUj6Ol7b9fBq5Uc12Wf7';
        $expectedResult = '<iframe class="media" src="http://www.youtube.com/embed/9BONcpuDcrc?list=PLFtT91yOzCD4ntUj6Ol7b9fBq5Uc12Wf7" data-media="http://www.youtube.com/embed/9BONcpuDcrc?list=PLFtT91yOzCD4ntUj6Ol7b9fBq5Uc12Wf7" data-type="youtube" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        $this->assertSame($expectedResult, $appExtension->createMediaHtmlFunction($mediaFolder, $url));
   }

}
