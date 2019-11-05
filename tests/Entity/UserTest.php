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
    public function testGetUserName()
    {
        $user = new User();
        $user->setUserName('JohnDoe824');
        $this->assertSame('JohnDoe824', $user->getUserName());
    }

    /**
     * Unit test for User entity
     *
     * @return void
     */
    public function testGetEmail()
    {
        $user = new User();
        $user->setEmail('john.doe824@orlinstreet.rocks');
        $this->assertSame('john.doe824@orlinstreet.rocks', $user->getEmail());
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

    /**
     * Unit test for User entity
     *
     * @return void
     */
    public function testGetToken()
    {
        $user = new User();
        $user->setToken('akBck4rwHvJ-WjbvO_d_3ekDbNfSuUL7eD4csdUI0ng');
        $this->assertSame('akBck4rwHvJ-WjbvO_d_3ekDbNfSuUL7eD4csdUI0ng', $user->getToken());
    }

    /**
     * Unit test for User entity
     *
     * @return void
     */
    public function testGetAvatar()
    {
        $user = new User();
        $user->setAvatar('person_1.jpg');
        $this->assertSame('person_1.jpg', $user->getAvatar());
    }

    /**
     * Unit test for User entity
     *
     * @return void
     */
    public function testGetAvatarUploadDir()
    {
        $user = new User();
        $this->assertSame('/avatars', $user->getAvatarUploadDir());
    }

}
