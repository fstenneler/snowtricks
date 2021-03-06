<?php

namespace App\Form\Handler\User;

use App\Entity\User;
use App\Services\FileUpload;
use Symfony\Component\Form\Form;
use App\Form\Handler\AbstractHandler;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ManageAvatarHandler extends AbstractHandler
{
    private $session;
    private $manager;
    private $fileUpload;

    public function __construct(
        SessionInterface $session,
        ObjectManager $manager,
        FileUpload $fileUpload
    )
    {
        $this->session = $session;
        $this->manager = $manager;
        $this->fileUpload = $fileUpload;
    }
    
    /**
     * Handle manage avatar form
     *
     * @param Request $request
     * @param Form $form
     * @param User $user
     * @return array
     */
    public function handle(Request $request, Form $form, User $user)
    {

        // handle requested data
        $this->form = $form->handleRequest($request);

        // if form is submitted and valid, store data, log the user and redirect to the account route
        if($this->form->isSubmitted() && $this->form->isValid()) {

            $avatarFile = $this->form['avatar']->getData();

            $uploadResult = $this->fileUpload->upload(
                $avatarFile,
                $user->getAbsoluteAvatarUploadPath()
            );
            
            if($uploadResult['success'] !== true) {
                $this->session->getFlashBag()->add('avatar-error', 'An unexpected error has occurred : ' . $uploadResult['error']);
                return ['success' => false];
            }

            // set the avatar value
            $user->setAvatar($uploadResult['fileName']);

            // store data into database
            $this->manager->persist($user);
            $this->manager->flush();

            return $this->setSuccess(true);

        }

        return $this->setSuccess(false);
    }

}
