<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Category;
use PHPUnit\Framework\TestCase;

class TrickTest extends TestCase
{
    /**
     * Unit test for Trick entity
     *
     * @return void
     */
    public function testGetName()
    {
        $trick = new Trick();
        $trick->setName('Test Name content');
        $this->assertSame('Test Name content', $trick->getName());
    }

    /**
     * Unit test for Trick entity
     *
     * @return void
     */
     public function testGetDescription()
    {
        $trick = new Trick();
        $trick->setDescription('Test description content');
        $this->assertSame('Test description content', $trick->getDescription());
    }

    /**
     * Unit test for Trick entity
     *
     * @return void
     */
     public function testGetCreationDate()
    {
        $trick = new Trick();
        $dateTime = new \DateTime();
        $trick->setCreationDate($dateTime);
        $this->assertSame($dateTime, $trick->getCreationDate());
    }

    /**
     * Unit test for Trick entity
     *
     * @return void
     */
     public function testGetModificationDate()
    {
        $trick = new Trick();
        $dateTime = new \DateTime();
        $trick->setModificationDate($dateTime);
        $this->assertSame($dateTime, $trick->getModificationDate());
    }


    /**
     * Unit test for Trick entity
     *
     * @return void
     */
     public function testGetCategory()
    {
        $trick = new Trick();
        $category = new Category();
        $trick->setCategory($category);
        $this->assertSame($category, $trick->getCategory());
    }

<<<<<<< HEAD

    /**
     * Unit test for setSlug and getSlug
     *
     * @return void
     */
    public function testGetSlug()
    {
        $trick = new Trick();
        $trick->setSlug('test-slug');
        $this->assertSame('test-slug', $trick->getSlug());
    }

=======
>>>>>>> 298483bc2ca9dac2dd5518824e2e3b89ceee8485
    /**
     * Unit test for Trick entity
     *
     * @return void
     */
    public function testGetUser()
    {
        $trick = new Trick();
        $user = new User();
        $trick->setUser($user);
        $this->assertSame($user, $trick->getUser());
    }

}
