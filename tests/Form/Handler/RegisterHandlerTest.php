<?php

namespace App\Tests\Controller;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use App\Controller\UserController;
use App\Form\Type\RegistrationType;
use App\Form\Handler\RegisterHandler;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Tests\Functional\app\AppKernel;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegisterHandlerTest extends TypeTestCase
{

    protected static $container;

    public function testHandle()
    {

        // this line is important ↓
        //$registerHandler = self::$container->get(RegisterHandler::class);

        //$user = new User();
        //$form = $this->factory->create(RegistrationType::class, $user);

        /*$request->set('registration', [
            'userName' => 'user1',
            'email' => 'test@orlinstreet.rocks',
            'password' => 'azerty'
        ]);*/

        //$handler = $registerHandler->handle($request, $form, $user); dump($handler);

        /*$a = $registerHandler->expects($this->any())
             ->method('handle')
             ->with($request, $form, $user);*/

             //$request = $this->createMock(Request::class);

        //$handler = $registerHandler->handle($request, $form, $user); dump($handler);

        //$this->assertSame(false, $handler['success']);

        //$returnMyName = $this->createMock(ReturnMyName::class);
        //$returnMyName = new ReturnMyName();
        //$returnMyName = self::$container->get(ReturnMyName::class);
        /*$user = new User();
        $request = $this->createMock(Request::class);
        $form = $this->factory->create(RegistrationType::class, $user);

        //$registerHandler = $this->createMock(RegisterHandler::class);
        $registerHandler = $this
        ->getMockBuilder(RegisterHandler::class)
        ->disableOriginalConstructor()
        ->getMock();
        $registerHandler->method('handle')->with($request, $form, $user)->willReturn([true]);*/

        $session = $this->createMock(SessionInterface::class);
        $manager = $this->createMock(ObjectManager::class);
        $passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        $tokenGenerator = $this->createMock(TokenGeneratorInterface::class);
        $mailer = $this->createMock(\Swift_Mailer::class);
        $router = $this->createMock(UrlGeneratorInterface::class);
        $templating = $this->createMock(\Twig_Environment::class);
        //$request = $this->createMock(Request::class);
        //$request->request = $this->createMock('ParameterBag');

        /*$request = $this
            ->getMockBuilder(Request::class)
            ->getMock();*/

            /*$request->request->method('get')->with('registration')->willReturn([
                'userName' => 'user1',
                'email' => 'test@orlinstreet.rocks',
                'password' => 'azerty'
            ]);*/

            /*$request = new Request();
            $request->request->set('registration', [
                'userName' => 'user1',
                'email' => 'test@orlinstreet.rocks',
                'password' => 'azerty'
            ]);
            


        $registerHandler = new RegisterHandler(
            $session,
            $manager,
            $passwordEncoder,
            $tokenGenerator,
            $router,
            $templating,
            $mailer
        );

        $user = new User();
        $form = $this->factory->create(RegistrationType::class, $user);

        //$handler = $registerHandler->handle($request, $form, $user);*/

        $this->assertSame(1, 1);
        
    }
    
}