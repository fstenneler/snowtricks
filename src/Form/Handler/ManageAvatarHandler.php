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

class ManageAvatarHandler
{
    private $session;
    private $manager;
    private $passwordEncoder;
    private $loginFormAuth;

    public function __construct(
        SessionInterface $session,
        ObjectManager $manager,
        UserPasswordEncoderInterface $passwordEncoder,
        LoginFormAuthenticator $loginFormAuth
    )
    {
        $this->session = $session;
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
        $this->loginFormAuth = $loginFormAuth;
    }
        
    public function handle(Request $request, Form $form, User $user)
    {

        // handle requested data
        $form->handleRequest($request);

        // if form is submitted and valid, store data, log the user and redirect to the account route
        if($form->isSubmitted() && $form->isValid()) {

            $avatarFile = $form['avatar']->getData();
            $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
               
            // this is needed to safely include the file name as part of the URL
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$avatarFile->guessExtension();

            // Move the file to the directory
            try {
                $avatarFile->move(
                    $user->getAbsoluteAvatarUploadPath(),
                    $newFilename
                );
            } catch (FileException $e) {
                $this->session->getFlashBag()->add('avatar-error', 'An unexpected error has occurred. Please try again.');
            }

            // set the avatar value
            $user->setAvatar($newFilename);

            // store data into database
            $this->manager->persist($user);
            $this->manager->flush();

            return ['success' => true];

        }

        return ['success' => false, 'form' => $form];
    }

}
