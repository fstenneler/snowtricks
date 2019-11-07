<?php

namespace App\Form\Handler;

use App\Entity\Comment;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CommentHandler
{
    private $manager;
    private $success = false;
    private $form;

    public function __construct(
        ObjectManager $manager
    )
    {
        $this->manager = $manager;
    }
    
    /**
     * Handle add comment form
     *
     * @param Request $request
     * @param Form $form
     * @param Comment $comment
     * @return self
     */
    public function handle(Request $request, Form $form, Comment $comment)
    {   

        // handle requested data
        $this->form = $form->handleRequest($request);

        // if form is submitted and valid
        if($this->form->isSubmitted() && $this->form->isValid()) {
            
            $comment->setCreationDate(new \DateTime);

            // store data into database
            $this->manager->persist($comment);
            $this->manager->flush();

            $this->success = true;
            return $this;

        }

        return $this;
    }

    /**
     * Get the comment form
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Get form handle success
     *
     * @return bool
     */
    public function getSuccess()
    {
        return $this->success;
    }

}
