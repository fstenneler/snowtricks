<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    public function index()
    {
        /**
         * @Route("/", name="home")
         */
        return $this->render('home.html.twig', [
            'foo' => 'Hello world !'
        ]);
    }
}
