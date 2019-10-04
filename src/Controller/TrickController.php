<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET","HEAD"})
     */
    public function index()
    {
        return $this->render('single.html.twig', [
            'foo' => 'Hello world !'
        ]);
    }
}
