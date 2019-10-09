<?php

namespace App\Tests;

use App\Entity\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{

    /**
     * Unit test for Category entity
     *
     * @return void
     */
     public function testGetName()
    {
        $category = new Category();
        $category->setName('Test Name content');
        $this->assertSame('Test Name content', $category->getName());
    }

}
