<?php

namespace App\Form\Handler;

use App\Entity\Media;
use App\Services\Video;
use App\Services\FileUpload;
use Symfony\Component\Form\Form;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class PictureHandler
{
    private $session;
    private $manager;
    private $fileUpload;
    private $success = false;
    private $form;
    private $video;

    public function __construct(
        SessionInterface $session,
        ObjectManager $manager,
        FileUpload $fileUpload,
        Video $video
    )
    {
        $this->session = $session;
        $this->manager = $manager;
        $this->fileUpload = $fileUpload;
        $this->video = $video;
    }
        
    public function handle(Request $request, Form $form, Media $media, $action)
    {   

        // handle requested data
        $this->form = $form->handleRequest($request);        

        // if form is submitted and valid
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
        
        // delete media mode
        if($action === "confirm_deletion" && !$this->video->isVideo($media->getUrl())) {
            $filesystem = new Filesystem();
            try {
                $filesystem->remove([
                    $media->getAbsoluteUploadPath() . '/' . $media->getUrl()
                ]);
            } catch (IOExceptionInterface $exception) {
                $this->addFlash('delete-media-error', 'An unexpected error has occurred while deleting the media file : ' . $exception->getMessage() .'.');
                return $this;
            }
            $this->manager->remove($media);
            $this->manager->flush();
            $this->session->getFlashBag()->add('delete-media-success', 'The media file has been deleted.');
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
