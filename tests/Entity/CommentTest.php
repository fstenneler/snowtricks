<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Comment;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    /**
     * Unit test for Comment entity
     *
     * @return void
     */
    public function testGetMessage()
    {
        $comment = new Comment();
        $comment->setMessage('Test Message content');
        $this->assertSame('Test Message content', $comment->getMessage());
    }

    /**
     * Unit test for Comment entity
     *
     * @return void
     */
     public function testGetCreationDate()
    {
        $comment = new Comment();
        $dateTime = new \DateTime();
        $comment->setCreationDate($dateTime);
        $this->assertSame($dateTime, $comment->getCreationDate());
    }

    /**
     * Unit test for Comment entity
     *
     * @return void
     */
     public function testGetTrick()
    {
        $comment = new Comment();
        $trick = new Trick();
        $comment->setTrick($trick);
        $this->assertSame($trick, $comment->getTrick());
    }

    /**
     * Unit test for Trick entity
     *
     * @return void
     */
    public function testGetUser()
    {
        $comment = new Comment();
        $user = new User();
        $comment->setUser($user);
        $this->assertSame($user, $comment->getUser());
    }

}
