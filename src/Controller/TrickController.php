<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Category;
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

        return $this->render('tricks/home.html.twig', [
            'trick_list' => $trick
        ]);
    }
    
    /**
    * @Route("/category/{categorySlug}", name="tricks", methods={"GET","HEAD"}, defaults={"categorySlug" = "all"})
    */
   public function tricks($categorySlug)
   {

       // 404 route for invalid category slugs
       if($categorySlug !== "all") {
           $category = $this->getDoctrine()->getRepository(Category::class)->findOneBySlug($categorySlug);
           if(!$category) {
               throw $this->createNotFoundException('Requested category slug not found');
           }
       }

       $trick = $this->getDoctrine()
           ->getRepository(Trick::class)
           ->findTricks(['categorySlug' => $categorySlug]);

       $category = $this->getDoctrine()
           ->getRepository(Trick::class)
           ->findCategories();

       return $this->render('tricks/tricks.html.twig', [
           'trick_list' => $trick,
           'category_list' => $category
       ]);

   }
 
    /**
     * @Route(
     *  "/load-results/{categorySlug}/{firstResult}/{orderBy}",
     *  name="load_results",
     *  methods={"GET"},
     *  requirements={"firstResult" = "\d+"},
     *  defaults={"categorySlug" = "all", "firstResult" = 1, "orderBy" = "creationDate-DESC"}
     * )
     */
     public function loadResults($categorySlug, $firstResult, $orderBy)
     {

        $trick = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findTricks([
                'categorySlug' => $categorySlug,
                'firstResult' => $firstResult,
                'orderBy' => $orderBy
            ]);
 
        return $this->render('tricks/_tricks.html.twig', [
            'trick_list' => $trick
        ]);
     }
 
    /**
     * @Route(
     *  "/load-comments/{trickId}/{firstResult}",
     *  name="load_comments",
     *  methods={"GET"},
     *  requirements={"trickId" = "\d+", "firstResult" = "\d+"},
     *  defaults={"trickId" = 0, "firstResult" = 1}
     * )
     */
    public function loadComments($trickId, $firstResult)
    {
       $comment = $this->getDoctrine()
           ->getRepository(Comment::class)
           ->findComments($trickId, $firstResult);

       return $this->render('tricks/_comment.html.twig', [
           'comment_list' => $comment
       ]);
    }
   
    /**
    * @Route("/tricks/{trickSlug}", name="single_trick", methods={"GET","HEAD"}, defaults={"trickSlug" = null})
    */
   public function singleTrick($trickSlug)
   {

        $trick = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findOneBy(['slug' => $trickSlug]);

        if(!$trick) {
            throw $this->createNotFoundException('Requested trick slug not found');
        }

        return $this->render('tricks/single.html.twig', [
            'trick' => $trick
        ]);

   }

 }
