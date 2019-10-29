<?php

namespace App\Form\Handler;

use App\Entity\User;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterHandler
{
    private $session;
    private $manager;
    private $passwordEncoder;
    private $loginFormAuth;

    public function __construct(
        SessionInterface $session,
        ObjectManager $manager,
        UserPasswordEncoderInterface $passwordEncoder,
        LoginFormAuthenticator $loginFormAuth
    )
    {
        $this->session = $session;
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
        $this->loginFormAuth = $loginFormAuth;
    }
        
    public function handle(Request $request, Form $form, User $user)
    {

        // handle requested data
        $form->handleRequest($request);

        // if form is submitted and valid, store data, log the user and redirect to the account route
        if($form->isSubmitted() && $form->isValid()) {

            // encode password
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $user->getPassword())
            );

            // store data into database
            $this->manager->persist($user);
            $this->manager->flush();
            
            // log user into session
            $this->loginFormAuth->logUser($user);
    
            $this->session->getFlashBag()->add('success', 'You are now successfully registered !');

            return ['success' => true];

        }

        return ['success' => false, 'form' => $form];
    }

}
