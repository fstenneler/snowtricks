<?php

namespace App\Form\Handler;


abstract class AbstractHandler {

    protected $success = false;
    protected $form;

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
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * Set form handle success
     *
     * @return self
     */
    public function setSuccess($success)
    {
        $this->success = $success;
        return $this;
    }

}
