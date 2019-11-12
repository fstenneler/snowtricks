<?php

namespace App\Form\Handler\Trick;

use App\Entity\Media;
use App\Services\Video;
use Symfony\Component\Form\Form;
use App\Form\Handler\AbstractHandler;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class VideoHandler extends AbstractHandler
{
    private $session;
    private $manager;
    private $video;

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
    
    /**
     * Handle video form
     *
     * @param Request $request
     * @param Form $form
     * @param Media $media
     * @param string $action Requested action : add, edit, delete
     * @return self
     */
    public function handle(Request $request, Form $form, Media $media, $action)
    {   

        // handle requested data
        $this->form = $form->handleRequest($request);

        // if form is submitted and valid
        if($this->form->isSubmitted() && $this->form->isValid()) {

            $url = $this->form['url']->getData();

            if(!$this->video->isVideoCodeCorrect($url)) {
                $this->form->get('url')->addError(new FormError('The given video integration code is not supported.'));
                return $this->setSuccess(false);
            }

            if(!$this->video->isIframe($url)) {
                $this->form->get('url')->addError(new FormError('Please give a iframe video integration code.'));
                return $this->setSuccess(false);
            }

            $media->setUrl($this->video->videoCodeToUrl($url));

            // store data into database
            $this->manager->persist($media);
            $this->manager->flush();
            
            $this->session->getFlashBag()->add('video-success', 'Your media file has been successfully uploaded.');
            return $this->setSuccess(true);

        }
        
        // delete media mode
        if($action === "confirm_deletion" && $this->video->isVideo($media->getUrl())) {
            $this->manager->remove($media);
            $this->manager->flush();
            $this->session->getFlashBag()->add('delete-media-success', 'The media file has been deleted.');
            return $this->setSuccess(true);
        } 

        // set form field to blank
        if(!$this->form->isSubmitted()) {
            $this->form->get('url')->setData('');
        }

        return $this->setSuccess(false);

    }

}
