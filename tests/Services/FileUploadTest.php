<?php

namespace App\Tests;

use App\Entity\User;
use App\Services\FileUpload;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadTest extends KernelTestCase
{
    protected static $container;
     
    public function setUp()
    {
        self::bootKernel();         
    }    

    /**
     * Unit test for send activation mail
     *
     * @return void
     */
    public function testUpload()
    {
   
        // Mock UploadedFile 
        $uploadedFile = $this
            ->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->getMock();  
        $uploadedFile
            ->method('move')
            ->willReturn(true);

        // Instanciate classes
        $fileUpload = new FileUpload();

        // call method to be tested
        $result = $fileUpload->upload($uploadedFile, __DIR__ . '..\..\public\avatars');

        // test if the two objects are the same
        $this->assertSame($result['success'], true);
        
    }

}
