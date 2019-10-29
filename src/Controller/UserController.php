<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\Handler\RegisterHandler;
use App\Form\Handler\ManageAccountHandler;
use App\Form\Handler\ResetPassswordHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Handler\ForgottenPassswordHandler;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{

    /**
     * @Route("/manage-account", methods={"GET","POST"}, name="manage_account")
     */
    public function manageAccount(Request $request, RegisterHandler $registerHandler, ManageAccountHandler $manageAccountHandler)
    {

        // register new user
        if(!$user = $this->getUser()) {
            $user = new User();
            $form = $this->createForm(RegistrationType::class, $user);
            $handler = $registerHandler->handle($request, $form, $user);
        } 
        
        // edit user
        else {
            $user = $this->getUser();
            $form = $this->createForm(RegistrationType::class);
            $handler = $manageAccountHandler->handle($request, $form, $user);
        }
        
        if($handler['success']) {
            return $this->redirectToRoute('manage_account');
        }

        return $this->render('user/manage_account.html.twig', [
            'form' => $handler['form']->createView(),
            'user' => $user
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
            ->findOneBy(['resetToken' => $token]);

        // if user not found, display an error message
        if(!$user) {
            $this->addFlash('error', 'Token error, please try again');
        } else {
            $handler = $resetpasswordHandler->handle($request, $user, $token);                
            if($handler['success']) {
                return $this->redirectToRoute('app_reset_password');
            }
        }

        return $this->render('user/reset_password.html.twig', ['token' => $token]);

    }

}
    