<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserController extends AbstractController
{

    /**
     * @Route("/manage-account", name="manage_account")
     */
    public function userForm(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder)
    {

        // check if user is authenticated, and if not create new User
        $createUser = false;
        if(!$user = $this->getUser()) {
            $user = new User();
            $createUser = true;
        }

        // create form with RegistrationType class
        $form = $this->createForm(RegistrationType::class, $user);

        // handle requested data
        $form->handleRequest($request);

        // if form is submitted and valid, store data, log the user and redirect to the account route
        if($form->isSubmitted() && $form->isValid()) {

            // encode password
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $user->getPassword())
            );

            // store data into database
            $manager->persist($user);
            $manager->flush();
            
            if($createUser) {

                // generate token object for created user, and store into session
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $this->container->get('security.token_storage')->setToken($token);
                $this->container->get('session')->set('_security_main', serialize($token));

                // store a flash message into session
                $this->addFlash('success', 'You are now successfully registered !');

            } else {

                // store a flash message into session
                $this->addFlash('success', 'Your account has been updated');

            }

            //redirect to account route
            return $this->redirectToRoute('manage_account');

        }

        return $this->render('user/manage_account.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        /* TODO :
        - déplacer le code de reset et forgotten password dans une classe du dossier security
        */
       
        // if user is already connected, redirect to user account
        if($this->getUser()) {
            return $this->redirectToRoute('manage_account');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        //throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/forgotten-password", methods={"GET","POST"}, name="app_forgotten_password")
     */
    public function forgottenPassword(Request $request, ObjectManager $manager, TokenGeneratorInterface $tokenGenerator, \Swift_Mailer $mailer): Response
    {
        // test if form is submitted
        if($request->isMethod('POST')) {

            // search the user with requested email
            $email = $request->request->get('email');
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['email' => $email]);

            // if user not found, refresh the page with an error message
            if(!$user) {
                $this->addFlash('error', 'Email address "' . $email . '" could not be found');
                return $this->redirectToRoute('app_forgotten_password');
            }

            // generate token
            $token = $tokenGenerator->generateToken();

            try {

                // save token into database
                $user->setResetToken($token);
                $manager->persist($user);
                $manager->flush();

                // generate an absolute url containing token
                $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL); 

                // send an email (HTML + txt) with the reset password link
                $message = (new \Swift_Message('Reset your password'))
                    ->setFrom('snowtricks@orlinstreet.rocks')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/reset_password.html.twig',
                            ['user' => $user, 'url' => $url]
                        ),
                        'text/html'
                    )
                    ->addPart(
                        $this->renderView(
                            'emails/reset_password.txt.twig',
                            ['user' => $user, 'url' => $url]
                        ),
                        'text/plain'
                    );
                $mailer->send($message);

            } catch(\Exception $e) {
                $this->addFlash('error', 'The message could not been sent : ' . $e);
                return $this->redirectToRoute('app_forgotten_password');
            }
            
            $this->addFlash('success', 'We just sent an email to this address');
            return $this->redirectToRoute('app_forgotten_password');

        }

        return $this->render('user/forgotten_password.html.twig');
    }

    /**
     * @Route("/reset-password/{token}", methods={"GET","POST"}, defaults={"token" = null}, name="app_reset_password")
     */
    public function resetPassword($token, Request $request, UserPasswordEncoderInterface $passwordEncoder, ObjectManager $manager)
    {

        // test if form is submitted
        if($request->isMethod('POST')) {

            // search the user with requested token
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['token' => $token]);

            // if user not found, refresh the page with an error message
            if(!$user) {
                $this->addFlash('error', 'Token error, please back try again');
                return $this->redirectToRoute('app_forgotten_password');
            }
            
            // empty the reset token for this user
            $user->setResetToken(null);

            // encode password
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $request->request->get('password'))
            );

            // save new password
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $manager->persist();
            $manager->flush();
 
            $this->addFlash('success', 'Your password has been successfully updated');
 
            return $this->redirectToRoute('app_reset_password');

        }

        return $this->render('user/reset_password.html.twig', ['token' => $token]);

    }

}
    