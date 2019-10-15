<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * Unit test for User entity
     *
     * @return void
     */
    public function testGetEmail()
    {
        $user = new User();
        $user->setEmail('user@domain.ext');
        $this->assertSame('user@domain.ext', $user->getEmail());
    }

    /**
     * Unit test for User entity
     *
     * @return void
     */
    public function testGetRoles()
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $this->assertSame(['ROLE_USER'], $user->getRoles());
    }

    /**
     * Unit test for User entity
     *
     * @return void
     */
    public function testGetPassword()
    {
        $user = new User();
        $user->setPassword('azerty');
        $this->assertSame('azerty', $user->getPassword());
    }


}
