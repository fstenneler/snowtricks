<?php

namespace App\Form\Handler;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Templating\EngineInterface;


class ForgottenPassswordHandler
{
    private $manager;
    private $tokenGenerator;
    private $mailer;
    private $session;
    private $router;

    public function __construct(
        ObjectManager $manager,
        TokenGeneratorInterface $tokenGenerator, 
        \Swift_Mailer $mailer,
        SessionInterface $session,
        UrlGeneratorInterface $router,
        \Twig_Environment $templating
    )
    {
        $this->manager = $manager;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
        $this->session = $session;
        $this->router = $router;
        $this->templating = $templating;
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

            // if user not found, refresh the page with an error message
            if(!$user) {
                $this->session->getFlashBag()->add('error', 'Email address "' . $email . '" could not be found');
                return ['success' => false];
            }

            // generate token
            $token = $this->tokenGenerator->generateToken();

            try {

                // save token into database
                $user->setResetToken($token);
                $this->manager->persist($user);
                $this->manager->flush();

                // generate an absolute url containing token
                $url = $this->router->generate('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL); 

                // send an email (HTML + txt) with the reset password link
                $message = (new \Swift_Message('Reset your password'))
                    ->setFrom('snowtricks@orlinstreet.rocks')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->templating->render(
                            'emails/reset_password.html.twig',
                            ['user' => $user, 'url' => $url]
                        ),
                        'text/html'
                    )
                    ->addPart(
                        $this->templating->render(
                            'emails/reset_password.txt.twig',
                            ['user' => $user, 'url' => $url]
                        ),
                        'text/plain'
                    );
                $this->mailer->send($message);

            } catch(\Exception $e) {
                $this->session->getFlashBag()->add('error', 'The message could not been sent : ' . $e);
                return ['success' => false];
            }
            
            $this->session->getFlashBag()->add('success', 'We just sent an email to this address');
            return ['success' => true];

        }

        return ['success' => false];
    }

}
