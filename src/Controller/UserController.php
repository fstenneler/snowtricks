<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\SendMail;
use App\Form\Type\AvatarType;
use App\Form\Type\RegistrationType;
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
            return $this->redirectToRoute('home');
        }

        return $this->render('user/manage_account.html.twig', [
            'form' => $handler['form']->createView()
        ]);
    }

    /**
     * @Route("/resend-activation-token/{userName}", methods={"GET"}, defaults={"userName" = null}, name="app_resend_activation_token")
     */
    public function reSendActivationToken(Request $request, SendMail $sendMail, $userName)
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
        $sendResult = $sendMail->sendActivationMail($user);
        if($sendResult !== true) {
            $this->addFlash('body-error', 'An unexpected error has occured while sending the activation mail : ' . $sendResult);
            return $this->redirectToRoute('app_login');
        }

        // redirect to login page
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

         // if user not found, display an error message and redirect to home
        if(!$user) {           
            $this->addFlash('body-error', 'Token error, please try again.');
            return $this->redirectToRoute('home');
        }

        // set user data
        $user->setToken('');
        $user->setActivated(true);
        $manager->persist($user);
        $manager->flush();

        $this->addFlash('body-success', 'Your account has been successfully activated ! Please login.');
        return $this->redirectToRoute('app_login');
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

        // get session user data
        $user = $this->getUser();

        // edit user
        $userForm = $this->createForm(RegistrationType::class);
        $userHandler = $manageAccountHandler->handle($request, $userForm, $user);

        // edit avatar
        $avatarForm = $this->createForm(AvatarType::class, $user);
        $avatarHandler = $manageAvatarHandler->handle($request, $avatarForm, $user);

        // if success, reload page
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
        // if user is already connected, redirect to user account
        if($this->getUser()) {
            return $this->redirectToRoute('manage_account');
        }

        // store last route into session
        $referer = $request->headers->get('referer');
        if(strpos($referer, $request->headers->get('host')) && strpos($referer, 'trick')) {
            $session->set('referer', $request->headers->get('referer'));
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
    }

    /**
     * @Route("/forgotten-password", methods={"GET","POST"}, name="app_forgotten_password")
     */
    public function forgottenPassword(Request $request, ForgottenPassswordHandler $forgottenPasswordHandler) : Response
    {
        $handler = $forgottenPasswordHandler->handle($request);     
        if($handler['success']) {
            $this->addFlash('body-success', 'We just sent you an email. Please check your mailbox and click the given link to reset your password.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/forgotten_password.html.twig');
    }

    /**
     * @Route("/reset-password/{token}", methods={"GET","POST"}, defaults={"token" = null}, name="app_reset_password")
     */
    public function resetPassword(Request $request, ResetPassswordHandler $resetpasswordHandler, $token)
    {        
        $handler = $resetpasswordHandler->handle($request, $token);   
        if($handler['success']) {
            $this->addFlash('body-success', 'Your password has been modified, please login.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/reset_password.html.twig', ['token' => $token]);

    }

}
