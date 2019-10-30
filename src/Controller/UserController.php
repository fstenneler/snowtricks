<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AvatarType;
use App\Form\RegistrationType;
use App\Form\Handler\RegisterHandler;
use App\Form\Handler\ManageAvatarHandler;
use App\Form\Handler\ManageAccountHandler;
use App\Form\Handler\ResetPassswordHandler;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Handler\ForgottenPassswordHandler;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{

    /**
     * @Route("/register", methods={"GET","POST"}, name="app_register")
     */
    public function register(Request $request, RegisterHandler $registerHandler)
    {

        // if user is already connected, redirect to user account
        if($this->getUser()) {
            return $this->redirectToRoute('manage_account');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $handler = $registerHandler->handle($request, $form, $user); 
        
        // redirect on success
        if($handler['success']) {
            return $this->redirectToRoute('manage_account');
        }

        return $this->render('user/manage_account.html.twig', [
            'form' => $handler['form']->createView()
        ]);
    }

    /**
     * @Route("/resend-activation-token/{userName}", methods={"GET"}, defaults={"userName" = null}, name="app_resend_activation_token")
     */
    public function reSendActivationToken(Request $request, RegisterHandler $registerHandler, $userName)
    {

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['userName' => $userName]);

        // if user not found redirect to login with an error
        if(!$user) {
            $this->addFlash('User name could not be found.');
            return $this->redirectToRoute('app_login');
        }

        // send activation mail
        if(!$registerHandler->sendActivationMail($user)) {
            $this->addFlash('body-error', 'An unexpected error has occured while sending the activation mail.');
            return $this->redirectToRoute('app_login');
        }

        $this->addFlash('body-success', 'We just sent you an email. Please check your mailbox and click the given link to activate your account.');
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/activate-account/{token}", methods={"GET","POST"}, defaults={"token" = null}, name="app_activate_account")
     */
    public function activateAccount(Request $request, ObjectManager $manager, $token)
    {
        
        // search the user with requested token
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['token' => $token]);

        if(!$user) {            

            // if user not found, display an error message and redirect to home
            $this->addFlash('body-error', 'Token error, please try again.');
            return $this->redirectToRoute('home');

        } else {

            // set user data
            $user->setToken('');
            $user->setActivated(true);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('body-success', 'Your account has been successfully activated ! Please login.');
            
            // redirect on success             
            return $this->redirectToRoute('app_login');

        }

        return $this->redirectToRoute('home');

    }

    /**
     * @Route("/manage-account", methods={"GET","POST"}, name="manage_account")
     */
    public function manageAccount(Request $request, ManageAccountHandler $manageAccountHandler, ManageAvatarHandler $manageAvatarHandler)
    {

        // if user is not connected, redirect to user login
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        // edit user
        $userForm = $this->createForm(RegistrationType::class);
        $userHandler = $manageAccountHandler->handle($request, $userForm, $user);

        // edit avatar
        $avatarForm = $this->createForm(AvatarType::class, $user);
        $avatarHandler = $manageAvatarHandler->handle($request, $avatarForm, $user);

        if($userHandler['success'] || $avatarHandler['success']) {
            return $this->redirectToRoute('manage_account');
        }

        return $this->render('user/manage_account.html.twig', [
            'form' => $userHandler['form']->createView(),
            'avatarForm' => $avatarHandler['form']->createView()
        ]);

    }

    /**
     * @Route("/login", methods={"GET","POST"}, name="app_login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils, Session $session): Response
    {       

        // store last route into session
        $referer = $request->headers->get('referer');
        if(strpos($referer, $request->headers->get('host')) && strpos($referer, 'trick')) {
            $session->set('referer', $request->headers->get('referer'));
        }

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
     * @Route("/logout", methods={"GET"}, name="app_logout")
     */
    public function logout()
    {
        //throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/forgotten-password", methods={"GET","POST"}, name="app_forgotten_password")
     */
    public function forgottenPassword(Request $request, ForgottenPassswordHandler $forgottenPasswordHandler) : Response
    {
        $handler = $forgottenPasswordHandler->handle($request);        
        // redirect on success
        if($handler['success']) {
            return $this->redirectToRoute('app_forgotten_password');
        }
        return $this->render('user/forgotten_password.html.twig');
    }

    /**
     * @Route("/reset-password/{token}", methods={"GET","POST"}, defaults={"token" = null}, name="app_reset_password")
     */
    public function resetPassword(Request $request, ResetPassswordHandler $resetpasswordHandler, $token)
    {
        
        // search the user with requested token
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['token' => $token]);

        if(!$user) {
        // if user not found, display an error message
        $this->addFlash('error', 'Token error, please try again');
        } else {
            // else handle form
            $handler = $resetpasswordHandler->handle($request, $user, $token);   
            // redirect on success             
            if($handler['success']) {
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('user/reset_password.html.twig', ['token' => $token]);

    }

}
    