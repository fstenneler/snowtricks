<?php

namespace App\Form\Handler;

use App\Entity\User;
use App\Entity\Trick;
use App\Services\Slug;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TrickHandler
{
    private $session;
    private $manager;
    private $success = false;
    private $form;
    private $slug;

    public function __construct(
        SessionInterface $session,
        ObjectManager $manager,
        Slug $slug
    )
    {
        $this->session = $session;
        $this->manager = $manager;
        $this->slug = $slug;
    }
        
    public function handle(Request $request, Form $form, Trick $trick, User $user, $action)
    {   

        // handle requested data
        $this->form = $form->handleRequest($request);

        // if form is submitted and valid
        if($this->form->isSubmitted() && $this->form->isValid()) {

            if($action === "add") {
                $trick->setUser($user);
                $trick->setCreationDate(new \DateTime);
                $trick->setSlug(
                    $this->slug->createSlug($trick->getName())
                );
            }

            if($action === "edit") {
                $trick->setmodificationDate(new \DateTime);
            }

            // store data into database
            $this->manager->persist($trick);
            $this->manager->flush();

            $this->success = true;
            $this->session->getFlashBag()->add('trick-success', 'The trick has been updated successfully.');
            return $this;

        }
        
        // delete trick mode
        if($action === "confirm_deletion") {
            $this->manager->remove($trick);
            $this->manager->flush();
            $this->success = true;
            $this->session->getFlashBag()->add('body-success', 'The trick file has been deleted successfully.');
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
