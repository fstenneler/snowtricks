<?php

namespace App\Form\Handler;

use App\Entity\User;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegisterHandler
{
    private $session;
    private $manager;
    private $passwordEncoder;
    private $tokenGenerator;
    private $router;
    private $templating;
    private $mailer;

    public function __construct(
        SessionInterface $session,
        ObjectManager $manager,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGeneratorInterface $tokenGenerator,
        UrlGeneratorInterface $router,
        \Twig_Environment $templating,
        \Swift_Mailer $mailer
    )
    {
        $this->session = $session;
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->router = $router;
        $this->templating = $templating;
        $this->mailer = $mailer;
    }

    /**
     * Handle form request
     *
     * @param Request $request
     * @param Form $form
     * @param User $user
     * @return array
     */        
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

            // generate token
            $token = $this->tokenGenerator->generateToken();
            $user->setToken($token);
            $user->setTokenCreationDate(new \DateTime());

            // save data into database
            $this->manager->persist($user);
            $this->manager->flush();

            // send activation mail
            if(!$this->sendActivationMail($user)) {
                $this->session->getFlashBag()->add('body-error', 'An unexpected error has occured while sending the activation mail');
                return ['success' => false];
            }
            
            $this->session->getFlashBag()->add('body-success', 'We just sent you an email. Please check your mailbox and click the given link to activate your account.');

            return ['success' => true];

        }

        return ['success' => false, 'form' => $form];
    }

    /**
     * Send activation mail
     *
     * @param User $user
     * @return bool
     */
    public function sendActivationMail(User $user)
    {

        $token = $user->getToken();

        if($token === null) {
            $this->session->getFlashBag()->add('body-error', 'The token is not defined, please try again.');
            return false;
        }

        // generate an absolute url containing token
        $url = $this->router->generate('app_activate_account', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL); 

        // send an email (HTML + txt) with the reset password link
        $message = (new \Swift_Message('Reset your password'))
            ->setFrom('snowtricks@orlinstreet.rocks')
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    'emails/activate_account.html.twig',
                    ['user' => $user, 'url' => $url]
                ),
                'text/html'
            )
            ->addPart(
                $this->templating->render(
                    'emails/activate_account.txt.twig',
                    ['user' => $user, 'url' => $url]
                ),
                'text/plain'
            );

        return $this->mailer->send($message);

    }

}
