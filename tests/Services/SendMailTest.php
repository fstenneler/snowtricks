<?php

namespace App\Tests;

use App\Entity\User;
use App\Services\SendMail;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SendMailTest extends KernelTestCase
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
    public function testSendActivationMail()
    {

        // Instanciate classes
        $sendMail = new SendMail(
            self::$container->get('router.default'),
            self::$container->get('twig'),
            self::$container->get('swiftmailer.mailer.default')
        );
        $user = new User();

        // define result expected
        $user->setToken('Y0DjDejxF6MswL0Ndyw6Ox8XT0z7MJFELHEse9VPWuM');
        $user->setEmail('user1@orlinstreet.rocks');

        // call method to be tested
        $result = $sendMail->sendActivationMail($user);

        // test if the two objects are the same
        $this->assertSame($result, true);
        
    }

    /**
     * Unit test for send activation mail
     *
     * @return void
     */
    public function testSendPasswordResetMail()
    {

        // Instanciate classes
        $sendMail = new SendMail(
            self::$container->get('router.default'),
            self::$container->get('twig'),
            self::$container->get('swiftmailer.mailer.default')
        );
        $user = new User();

        // define result expected
        $user->setToken('Y0DjDejxF6MswL0Ndyw6Ox8XT0z7MJFELHEse9VPWuM');
        $user->setEmail('user1@orlinstreet.rocks');

        // call method to be tested
        $result = $sendMail->sendPasswordResetMail($user);

        // test if the two objects are the same
        $this->assertSame($result, true);
        
    }

}
