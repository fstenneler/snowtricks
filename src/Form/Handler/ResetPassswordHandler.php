<?php

namespace App\Form\Handler;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class ResetPassswordHandler
{
    private $manager;
    private $session;
    private $passwordEncoder;

    public function __construct(
        ObjectManager $manager,
        SessionInterface $session,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->manager = $manager;
        $this->session = $session;
        $this->passwordEncoder = $passwordEncoder;
    }
        
    public function handle(Request $request, User $user, $token)
    {

        // test if form is submitted
        if($request->isMethod('POST')) {

            $newPassword = $request->request->get('password');
            
            // check if the password is correct     
            if(strlen($newPassword) < 6) {
                $this->session->getFlashBag()->add('error', 'Your password must be at least 6 characters long.');
                return ['success' => false];
            } elseif(strlen($newPassword) > 20) {
                $this->session->getFlashBag()->add('error', 'Your password cannot ne longer than 20 characters.');
                return ['success' => false];
            }

            // if token was created more than 30 days before, display an error message
            if($user->getTokenCreationDate()->diff(new \DateTime()) > 30) {
                $this->addFlash('error', 'This email was sent over 30 days ago, please try again.');
                return ['success' => false];
            }

            try {
            
                // empty the reset token for this user
                $user->setToken('');

                // encode password
                $user->setPassword(
                    $this->passwordEncoder->encodePassword($user, $newPassword)
                );

                // save new password
                $user->setPassword($this->passwordEncoder->encodePassword($user, $request->request->get('password')));
                $this->manager->persist($user);
                $this->manager->flush();
    
                $this->session->getFlashBag()->add('body-success', 'Your password has been successfully updated');
    
                return ['success' => true];

            } catch(\Exception $e) {

                $this->session->getFlashBag()->add('error', 'An unexpected error has occurred : ' . $e);

            }

            return ['success' => false];

        }

    }

}
