<?php

namespace App\Services;

use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SendMail
{

    private $router;
    private $templating;
    private $mailer;

    public function __construct(UrlGeneratorInterface $router, \Twig_Environment $templating, \Swift_Mailer $mailer)
    {
        $this->router = $router;
        $this->templating = $templating;
        $this->mailer = $mailer;
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

        if($token === '') {
            return 'The token is not defined, please try again.';
        }

        // generate an absolute url containing token
        $url = $this->router->generate('app_activate_account', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL); 

        // send an email (HTML + txt) with the reset password link
        return $this->sendMail(
            $user->getEmail(),
            'Activate your account',
            $this->templating->render('emails/activate_account.html.twig', ['user' => $user, 'url' => $url]),
            $this->templating->render('emails/activate_account.txt.twig', ['user' => $user, 'url' => $url])
        );

    }

    /**
     * Send password reset mail
     *
     * @param User $user
     * @return bool
     */
    public function sendPasswordResetMail(User $user)
    {
        $token = $user->getToken();

        if($token === null) {
            return 'The token is not defined, please try again.';
        }

        // generate an absolute url containing token
        $url = $this->router->generate('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL); 

        // send an email (HTML + txt) with the reset password link
        return $this->sendMail(
            $user->getEmail(),
            'Reset your password',
            $this->templating->render('emails/reset_password.html.twig', ['user' => $user, 'url' => $url]),
            $this->templating->render('emails/reset_password.txt.twig', ['user' => $user, 'url' => $url])
        );

    }

    private function sendMail($recipient, $subject, $html, $txt) {

        $message = (new \Swift_Message($subject))
            ->setFrom('snowtricks@orlinstreet.rocks')
            ->setTo($recipient)
            ->setBody(
                $html,
                'text/html'
            )
            ->addPart(
                $txt,
                'text/plain'
            );
        
        try {
            $this->mailer->send($message);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return true;

    }

}
