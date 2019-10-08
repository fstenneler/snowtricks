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
            ->findLatest();

        if (!$trick) {
            throw $this->createNotFoundException(
                'No tricks found'
            );
        }

        return $this->render('home/home.html.twig', [
            'trick_list' => $trick
        ]);
    }

    /**
     * @Route("/load-more/{firstResult}", methods={"GET"}, name="load_more")
     */
     public function loadMore($firstResult)
     {
 
         $trick = $this->getDoctrine()
             ->getRepository(Trick::class)
             ->findLatest($firstResult);
 
         if (!$trick) {
             throw $this->createNotFoundException(
                 'No tricks found'
             );
         }
 
         return $this->render('home/_tricks.html.twig', [
             'trick_list' => $trick
         ]);
     }
 
    ///**
    // * @Route("/load-more/{tricksNumber}", methods={"GET"}, name="load_more")
    // */
     /*public function loadMore(SerializerInterface $serializer)
     {
 
         $trick = $this->getDoctrine()
             ->getRepository(Trick::class)
             ->findLatest();
 
         if (!$trick) {
             throw $this->createNotFoundException(
                 'No tricks found'
             );
         }

        // Serialize your object in Json
        $jsonObject = $serializer->serialize($trick, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
            'ignored_attributes' => ['description', 'category', 'creationDate', 'modificationDate', 'header']
        ]);

        // For instance, return a Response with encoded Json
        return new Response($jsonObject, 200, ['Content-Type' => 'application/json']);

     }*/
 }
