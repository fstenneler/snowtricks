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
    
    /**
     * Handle trick edit form
     *
     * @param Request $request
     * @param Form $form
     * @param Trick $trick
     * @param User $user
     * @param string $action Type of action requested : create, add, edit, delete, confirm_deletion
     * @return array
     */
    public function handle(Request $request, Form $form, Trick $trick, User $user, $action)
    {   
        
        // if delete trick mode with confirmation, remove trick and set success message
        if($action === "confirm_deletion") {
            $this->manager->remove($trick);
            $this->manager->flush();
            $this->success = true;
            $this->session->getFlashBag()->add('body-success', 'The trick file has been deleted successfully.');
            return $this;
        } 

        // handle requested data
        $this->form = $form->handleRequest($request);

        // if form is submitted and valid
        if($this->form->isSubmitted() && $this->form->isValid()) {

            // if new trick, set user and creation date 
            if($action === "create") {
                $trick->setUser($user);
                $trick->setCreationDate(new \DateTime);
            }

            // if edit set modification date
            if($action === "edit") {
                $trick->setmodificationDate(new \DateTime);
            }

            // set slug
            $trick->setSlug(
                $this->slug->createSlug($trick->getName())
            );           

            // store data into database
            $this->manager->persist($trick);
            $this->manager->flush();

            // set success
            $this->success = true;

            // set flash messages
            if($action === 'add') {
                $this->session->getFlashBag()->add('body-success', 'The trick has been added successfully.');
            } elseif($action !== 'create') {
                $this->session->getFlashBag()->add('trick-success', 'The trick has been updated successfully.');
            }

            return $this;

        }

        return $this;
    }

    /**
     * Get trick form
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Get handle success
     *
     * @return bool
     */
    public function getSuccess()
    {
        return $this->success;
    }

}
