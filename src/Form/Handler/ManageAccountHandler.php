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

class ManageAccountHandler
{
    private $session;
    private $manager;
    private $passwordEncoder;
    private $loginFormAuth;
    private $csrfTokenManager;

    public function __construct(
        SessionInterface $session,
        ObjectManager $manager,
        UserPasswordEncoderInterface $passwordEncoder,
        LoginFormAuthenticator $loginFormAuth,
        CsrfTokenManagerInterface $csrfTokenManager
    )
    {
        $this->session = $session;
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
        $this->loginFormAuth = $loginFormAuth;
        $this->csrfTokenManager = $csrfTokenManager;
    }
        
    public function handle(Request $request, Form $form, User $user)
    {

        if($request->isMethod('POST')) {

            $formIsValid = true;

            $newEmail = $request->request->get('registration')['email'];
            $newUserName = $request->request->get('registration')['userName'];
            $newPassword = $request->request->get('registration')['password'];

            $form->get('userName')->setData($newUserName);
            $form->get('email')->setData($newEmail);

            $token = new CsrfToken('registration', $request->request->get('registration')['_token']);
            if (!$this->csrfTokenManager->isTokenValid($token)) {
                throw new InvalidCsrfTokenException();
            }

            // check if the userName is used by another user
            if($this->manager->getRepository(User::class)->findOneBy(['userName' => $newUserName]) && $newUserName !== $user->getUserName()) {
                $form->get('userName')->addError(new FormError('The userName ' . $newUserName . ' is already used.'));
                $formIsValid = false;
            }
    
            // check if the email is used by another user
            if($this->manager->getRepository(User::class)->findOneBy(['email' => $newEmail]) && $newEmail !== $user->getEmail()) {
                $form->get('email')->addError(new FormError('The email address ' . $newEmail . ' is already used.'));
                $formIsValid = false;
            }
    
            // check if the password is correct     
            if(strlen($newPassword) < 6) {
                $form->get('password')->addError(new FormError('Your password must be at least 6 characters long.'));
                $formIsValid = false;
            } elseif(strlen($newPassword) > 20) {
                $form->get('password')->addError(new FormError('Your password cannot ne longer than 20 characters.'));
                $formIsValid = false;
            }
       
            if($formIsValid) {

                // set user data
                $user->setUserName($newUserName);
                $user->setEmail($newEmail);
                $user->setPassword($newPassword);

                // encode password
                $user->setPassword(
                    $this->passwordEncoder->encodePassword($user, $user->getPassword())
                );

                // store data into database
                $this->manager->persist($user);
                $this->manager->flush();
                
                // log user into session
                $this->loginFormAuth->logUser($user);

                $this->session->getFlashBag()->add('success', 'You account has been successfully modified !');

                return ['success' => true];

            }

            return ['success' => false, 'form' => $form];

        } else {

            $form->get('userName')->setData($user->getUserName());
            $form->get('email')->setData($user->getEmail());

        }
    

        return ['success' => false, 'form' => $form];
        
    }


}
