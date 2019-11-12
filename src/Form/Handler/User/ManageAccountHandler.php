<?php

namespace App\Form\Handler\User;

use App\Entity\User;
use Symfony\Component\Form\Form;
use App\Form\Handler\AbstractHandler;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ManageAccountHandler extends AbstractHandler
{
    private $session;
    private $manager;
    private $passwordEncoder;
    private $csrfTokenManager;

    public function __construct(
        SessionInterface $session,
        ObjectManager $manager,
        UserPasswordEncoderInterface $passwordEncoder,
        CsrfTokenManagerInterface $csrfTokenManager
    )
    {
        $this->session = $session;
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
        $this->csrfTokenManager = $csrfTokenManager;
    }
        
    /**
     * Handle manage account form
     *
     * @param Request $request
     * @param Form $form
     * @param User $user
     * @return array
     */
    public function handle(Request $request, Form $form, User $user)
    {

        $this->form = $form;

        // set default values to register form
        $this->form->get('userName')->setData($user->getUserName());
        $this->form->get('email')->setData($user->getEmail());

        // update account form
        if($request->isMethod('POST') && $request->request->get('registration')) {

            $formIsValid = true;

            // get form values
            $newEmail = $request->request->get('registration')['email'];
            $newUserName = $request->request->get('registration')['userName'];
            $newPassword = $request->request->get('registration')['password'];

            // set requested values to form
            $this->form->get('userName')->setData($newUserName);
            $this->form->get('email')->setData($newEmail);

            // test csrf token
            $token = new CsrfToken('registration', $request->request->get('registration')['_token']);
            if (!$this->csrfTokenManager->isTokenValid($token)) {
                throw new InvalidCsrfTokenException();
            }

            // check if the userName is used by another user
            if($this->manager->getRepository(User::class)->findOneBy(['userName' => $newUserName]) && $newUserName !== $user->getUserName()) {
                $this->form->get('userName')->addError(new FormError('The userName ' . $newUserName . ' is already used.'));
                $formIsValid = false;
            }
    
            // check if the email is used by another user
            if($this->manager->getRepository(User::class)->findOneBy(['email' => $newEmail]) && $newEmail !== $user->getEmail()) {
                $this->form->get('email')->addError(new FormError('The email address ' . $newEmail . ' is already used.'));
                $formIsValid = false;
            }
    
            // check if the password is correct     
            if(strlen($newPassword) < 6) {
                $this->form->get('password')->addError(new FormError('Your password must be at least 6 characters long.'));
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

                $this->session->getFlashBag()->add('success', 'You account has been successfully modified !');

                return $this->setSuccess(true);

            }

            return $this->setSuccess(false);

        }
    
        return $this->setSuccess(false);
        
    }

}
