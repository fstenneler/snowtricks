<?php

namespace App\Form\Handler;

use App\Entity\Media;
use App\Services\Video;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class VideoHandler
{
    private $session;
    private $manager;
    private $video;
    private $success = false;
    private $form;

    public function __construct(
        SessionInterface $session,
        ObjectManager $manager,
        Video $video
    )
    {
        $this->session = $session;
        $this->manager = $manager;
        $this->video = $video;
    }
        
    public function handle(Request $request, Form $form, Media $media, $action)
    {   

        // handle requested data
        $this->form = $form->handleRequest($request);

        // if form is submitted and valid
        if($this->form->isSubmitted() && $this->form->isValid()) {

            $url = $this->form['url']->getData();

            if(!$this->video->isVideoCodeCorrect($url)) {
                $this->session->getFlashBag()->add('video-error', 'The given video integration code is not supported.');
                return $this;
            }

            if(!$this->video->isIframe($url)) {
                $this->session->getFlashBag()->add('video-error', 'Please give a iframe video integration code.');
                return $this;
            }

            $media->setUrl($this->video->videoCodeToUrl($url));

            // store data into database
            $this->manager->persist($media);
            $this->manager->flush();

            $this->success = true;
            $this->session->getFlashBag()->add('video-success', 'Your media file has been successfully uploaded.');
            return $this;

        }
        
        // delete media mode
        if($action === "confirm_deletion" && $this->video->isVideo($media->getUrl())) {
            $this->manager->remove($media);
            $this->manager->flush();
            $this->session->getFlashBag()->add('delete-media-success', 'The media file has been deleted.');
            return $this;
        } 

        $this->form->get('url')->setData('');

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
