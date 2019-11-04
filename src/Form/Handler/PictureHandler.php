<?php

namespace App\Form\Handler;

use App\Entity\Media;
use App\Services\FileUpload;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PictureHandler
{
    private $session;
    private $manager;
    private $fileUpload;
    private $success = false;
    private $form;

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
        
    public function handle(Request $request, Form $form, Media $media)
    {   

        // handle requested data
        $this->form = $form->handleRequest($request);        

        // if form is submitted and valid, store data, log the user and redirect to the account route
        if($this->form->isSubmitted() && $this->form->isValid()) {

            $file = $this->form['url']->getData();

            $uploadResult = $this->fileUpload->upload(
                $file,
                $media->getAbsoluteUploadPath()
            );
            
            if($uploadResult['success'] !== true) {
                $this->session->getFlashBag()->add('picture-error', 'An unexpected error has occurred : ' . $uploadResult['error']);
                return $this;
            }

            // set the avatar value
            $media->setUrl($uploadResult['fileName']);

            // store data into database
            $this->manager->persist($media);
            $this->manager->flush();

            $this->success = true;
            $this->session->getFlashBag()->add('picture-success', 'Your media file has been successfully uploaded.');
            return $this;

        }

        return $this;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function getSuccess()
    {
        return $this->success;
    }

}
