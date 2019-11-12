<?php

namespace App\Form\Handler\User;

use App\Entity\User;
use App\Services\SendMail;
use App\Services\GenerateToken;
use Symfony\Component\Form\Form;
use App\Form\Handler\AbstractHandler;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterHandler extends AbstractHandler
{
    private $session;
    private $manager;
    private $passwordEncoder;
    private $sendMail;
    private $generateToken;

    public function __construct(
        SessionInterface $session,
        ObjectManager $manager,
        UserPasswordEncoderInterface $passwordEncoder,
        SendMail $sendMail,
        GenerateToken $generateToken
    )
    {
        $this->session = $session;
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
        $this->sendMail = $sendMail;
        $this->generateToken = $generateToken;
    }

    /**
     * Handle register form
     *
     * @param Request $request
     * @param Form $form
     * @param User $user
     * @return array
     */        
    public function handle(Request $request, Form $form, User $user)
    {

        // handle requested data
        $this->form = $form->handleRequest($request);

        // if form is submitted and valid, store data, log the user and redirect to the account route
        if($this->form->isSubmitted() && $this->form->isValid()) {

            // encode password
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $user->getPassword())
            );

            // generate token
            $user = $this->generateToken->generate($user);
            if($user->getToken() === null) {
                $this->session->getFlashBag()->add('body-error', 'An unexpected error has occured while sending the activation mail : Token error');
                return $this->setSuccess(false);
            }

            // save data into database
            $this->manager->persist($user);
            $this->manager->flush();
            
            // send activation mail
            $sendResult = $this->sendMail->sendActivationMail($user);
            if($sendResult !== true) {
                $this->session->getFlashBag()->add('body-error', 'An unexpected error has occured while sending the activation mail : ' . $sendResult);
                return $this->setSuccess(false);
            }
            
            $this->session->getFlashBag()->add('body-success', 'We just sent you an email. Please check your mailbox and click the given link to activate your account.');
            return $this->setSuccess(true);

        }

        return $this;
    }

}
