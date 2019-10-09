<?php

namespace App\Controller;

use App\Entity\Trick;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET","HEAD"})
     */
    public function index()
    {

        $trick = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findTricks();

        return $this->render('home/home.html.twig', [
            'trick_list' => $trick
        ]);
    }
    
    /**
    * @Route("/tricks/{categoryId}", name="tricks", methods={"GET","HEAD"}, requirements={"categoryId" = "\d+"}, defaults={"categoryId" = 0})
    */
    public function tricks($categoryId)
    {

        $trick = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findTricks(['categoryId' => $categoryId]);

        $category = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findCategories();

        return $this->render('tricks/tricks.html.twig', [
            'trick_list' => $trick,
            'category_list' => $category
        ]);

    }
 
    /**
     * @Route("/load-results/{categoryId}/{firstResult}/{orderBy}", methods={"GET"}, name="load_more", requirements={"firstResult" = "\d+", "categoryId" = "\d+"})
     */
     public function loadResults($categoryId, $firstResult, $orderBy)
     {

        $trick = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findTricks([
                'categoryId' => $categoryId,
                'firstResult' => $firstResult,
                'orderBy' => $orderBy
            ]);
 
        return $this->render('tricks/_tricks.html.twig', [
            'trick_list' => $trick
        ]);
     }

 }
