<?php

namespace App\Tests;

use App\Entity\Media;
use App\Entity\Trick;
use PHPUnit\Framework\TestCase;

class MediaTest extends TestCase
{

    /**
     * Unit test for setUrl and setUrl
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
     * Unit test for setTrick and getTrick
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

    /**
     * Unit test for getAbsoluteUploadPath
     *
     * @return void
     */
    public function testGetAbsoluteUploadPath()
    {
        $absolutePath = __DIR__ . '/../../public/gallery';
        $absolutePath = preg_replace("#tests#", "src", $absolutePath);
        $media = new Media();
        $this->assertSame($absolutePath, $media->getAbsoluteUploadPath());
    }

    /**
     * Unit test for getUploadDir
     *
     * @return void
     */
    public function testGetUploadDir()
    {
        $media = new Media();
        $this->assertSame('/gallery', $media->getUploadDir());
    }

}
