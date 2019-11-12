<?php

namespace App\Form\Handler\Trick;

use App\Entity\Comment;
use Symfony\Component\Form\Form;
use App\Form\Handler\AbstractHandler;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

class CommentHandler extends AbstractHandler
{
    private $manager;

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

            return $this->setSuccess(true);

        }

        return $this->setSuccess(false);

    }

}
