<?php

namespace App\Form\Handler;

use App\Entity\User;
use App\Services\SendMail;
use App\Services\GenerateToken;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


class ForgottenPassswordHandler
{
    private $session;
    private $manager;
    private $sendMail;
    private $generateToken;

    public function __construct(
        SessionInterface $session,
        ObjectManager $manager,
        SendMail $sendMail,
        GenerateToken $generateToken
    )
    {
        $this->session = $session;
        $this->manager = $manager;
        $this->sendMail = $sendMail;
        $this->generateToken = $generateToken;
    }
        
    public function handle(Request $request)
    {

        // test if form is submitted
        if($request->isMethod('POST')) {

            // search the user with requested email
            $email = $request->request->get('email');
            $user = $this->manager
                ->getRepository(User::class)
                ->findOneBy(['email' => $email]);
            if(!$user) {
                $this->session->getFlashBag()->add('error', 'Email address "' . $email . '" could not be found');
                return ['success' => false];
            }

            // generate token
            $user = $this->generateToken->generate($user);
            if($user->getToken() === null) {
                $this->session->getFlashBag()->add('body-error', 'An unexpected error has occured while sending the activation mail : Token error');
                return ['success' => false];
            }
            
            // save token into database
            $this->manager->persist($user);            
            $this->manager->flush();
            
            // send password reset mail
            $sendResult = $this->sendMail->sendPasswordResetMail($user);
            if($sendResult !== true) {
                $this->session->getFlashBag()->add('error', 'An unexpected error has occured while sending the password reset mail : ' . $sendResult);
                return ['success' => false];
            }

            return ['success' => true];

        }

        return ['success' => false];
    }

}
