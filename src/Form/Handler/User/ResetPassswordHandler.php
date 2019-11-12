<?php

namespace App\Form\Handler\User;

use App\Entity\User;
use Symfony\Component\Form\Form;
use App\Form\Handler\AbstractHandler;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class ResetPassswordHandler extends AbstractHandler
{
    private $session;
    private $passwordEncoder;
    private $manager;

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
    
    /**
     * Handle reset password form
     *
     * @param Request $request
     * @param string $token
     * @return array
     */
    public function handle(Request $request, Form $form, User $user, $token)
    {

        // handle requested data
        $this->form = $form->handleRequest($request);        

        // test if form is submitted
        if($this->form->isSubmitted() && $this->form->isValid()) {
            
            // empty the reset token for this user
            $user->setToken('');
            
            // encode password
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $user->getPassword())
            );

            // save data
            $this->manager->persist($user);
            $this->manager->flush();

            $this->session->getFlashBag()->add('body-success', 'Your password has been modified, please login.');
            return $this->setSuccess(true);

        }

        return $this->setSuccess(false);

    }

}
