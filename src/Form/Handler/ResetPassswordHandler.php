<?php

namespace App\Form\Handler;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class ResetPassswordHandler
{
    private $manager;
    private $session;
    private $passwordEncoder;
    private $em;

    public function __construct(
        ObjectManager $manager,
        SessionInterface $session,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em
    )
    {
        $this->manager = $manager;
        $this->session = $session;
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
    }
    
    /**
     * Handle reset password form
     *
     * @param Request $request
     * @param string $token
     * @return array
     */
    public function handle(Request $request, $token)
    {
        // search the user with requested token
        $user = $this->em
            ->getRepository(User::class)
            ->findOneBy(['token' => $token]);

        // if user not found, display an error message
        if(!$user) {            
            $this->session->getFlashBag()->add('error', 'Token error, please try again');
            return ['success' => false];
        }

        // test if form is submitted
        if($request->isMethod('POST')) {

            $newPassword = $request->request->get('password');
            
            // check if the password is correct     
            if(strlen($newPassword) < 6) {
                $this->session->getFlashBag()->add('error', 'Your password must be at least 6 characters long.');
                return ['success' => false];
            }

            // if token was created more than 30 days before, display an error message
            if($user->getTokenCreationDate()->diff(new \DateTime())->days > 30) {
                $this->addFlash('error', 'This email was sent over 30 days ago, please try again.');
                return ['success' => false];
            }
            
            // empty the reset token for this user
            $user->setToken('');
            
            // encode password
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $newPassword)
            );

            // set new password
            $user->setPassword($this->passwordEncoder->encodePassword($user, $request->request->get('password')));

            // save data
            $this->manager->persist($user);
            $this->manager->flush();

            return ['success' => true];

        }

    }

}
