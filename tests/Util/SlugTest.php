<?php

namespace App\Tests;

use App\Util\Slug;
use PHPUnit\Framework\TestCase;

class SlugTest extends TestCase
{
    /**
     * Unit test for Slug class
     *
     * @return void
     */
    public function testCreateSlug()
    {
        $name = "~test*$ )éà;";
        $this->assertSame("-test----ea-", Slug::createSlug($name));
    }

}
