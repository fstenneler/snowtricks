<?php

namespace App\Form\Handler\User;

use App\Entity\User;
use App\Services\SendMail;
use App\Services\GenerateToken;
use Symfony\Component\Form\Form;
use App\Form\Handler\AbstractHandler;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class ForgottenPassswordHandler extends AbstractHandler
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
    
    /**
     * Handle forgotten password form
     *
     * @param Request $request
     * @return array
     */
    public function handle(Request $request, Form $form)
    {

        // handle requested data
        $this->form = $form->handleRequest($request);
        
        // test if form is submitted
        if($this->form->isSubmitted() && $this->form->isValid()) {

            $email = $form->getData()['email'];

            // search the user with requested email
            $user = $this->manager
                ->getRepository(User::class)
                ->findOneBy(['email' => $email]);
            if(!$user) {
                $form->get('email')->addError(new FormError('Email address "' . $email . '" could not be found'));
                return $this->setSuccess(false);
            }

            // generate token
            $user = $this->generateToken->generate($user);
            if($user->getToken() === null) {
                $this->session->getFlashBag()->add('error', 'An unexpected error has occured while sending the activation mail : Token error');
                return $this->setSuccess(false);
            }
            
            // save token into database
            $this->manager->persist($user);            
            $this->manager->flush();
            
            // send password reset mail
            $sendResult = $this->sendMail->sendPasswordResetMail($user);
            if($sendResult !== true) {
                $this->session->getFlashBag()->add('error', 'An unexpected error has occured while sending the password reset mail : ' . $sendResult);
                return $this->setSuccess(false);
            }
            
            $this->session->getFlashBag()->add('body-success', 'We just sent you an email. Please check your mailbox and click the given link to reset your password.');
            return $this->setSuccess(true);

        }

        return $this->setSuccess(false);

    }


}
