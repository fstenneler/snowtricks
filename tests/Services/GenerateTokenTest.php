<?php

namespace App\Tests;

use App\Entity\User;
use App\Services\GenerateToken;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class GenerateTokenTest extends TestCase
{
    /**
     * Unit test for GenerateToken class
     *
     * @return void
     */
    public function testGenerate()
    {

        // test token value
        $token = 'Y0DjDejxF6MswL0Ndyw6Ox8XT0z7MJFELHEse9VPWuM';

        // Mock TokenGeneratorInterface 
        $tokenGenerator = $this
            ->getMockBuilder(TokenGeneratorInterface::class)
            ->getMock();
        $tokenGenerator
            ->method('generateToken')
            ->willReturn($token);

        // Instanciate classes
        $generateToken = new GenerateToken($tokenGenerator);
        $user = new User();

        // define result expected
        $expectedUser = $user;
        $expectedUser->setToken($token);

        // call method to be tested
        $user = $generateToken->generate($user);

        // test if the two objects are the same
        $this->assertSame($expectedUser, $user);
        
    }

}
