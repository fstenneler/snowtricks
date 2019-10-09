<?php

namespace App\Tests;

use App\Entity\Media;
use App\Entity\Trick;
use PHPUnit\Framework\TestCase;

class MediaTest extends TestCase
{

    /**
     * Unit test for Media entity
     *
     * @return void
     */
     public function testGetUrl()
    {
        $media = new Media();
        $media->setUrl('test_url_content.jpg');
        $this->assertSame('test_url_content.jpg', $media->getUrl());
    }

    /**
     * Unit test for Media entity
     *
     * @return void
     */
     public function testIsHeader()
    {
        $media = new Media();
        $media->setHeader(false);
        $this->assertSame(false, $media->isHeader());
    }

    /**
     * Unit test for Media entity
     *
     * @return void
     */
     public function testGetTrick()
    {
        $media = new Media();
        $trick = new Trick();
        $media->setTrick($trick);
        $this->assertSame($trick, $media->getTrick());
    }

}
