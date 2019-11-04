<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\Type\TrickType;
use App\Form\Type\VideoType;
use App\Form\Type\PictureType;
use App\Form\Handler\VideoHandler;
use App\Form\Handler\PictureHandler;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    * @Route("/category/{categorySlug}", name="tricks", methods={"GET","HEAD"}, defaults={"categorySlug" = "all"}, requirements={"categorySlug"="[a-z0-9\-]*"})
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
     *      "/load-results/{categorySlug}/{firstResult}/{orderBy}",
     *      name="load_results",
     *      methods={"GET"},
     *      requirements={"firstResult" = "\d+", "categorySlug"="[a-z0-9\-]*"},
     *      defaults={"categorySlug" = "all", "firstResult" = 1, "orderBy" = "creationDate-DESC"}
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
     *      "/load-comments/{trickId}/{firstResult}",
     *      name="load_comments",
     *      methods={"GET"},
     *      requirements={"trickId" = "\d+", "firstResult" = "\d+"},
     *      defaults={"trickId" = 0, "firstResult" = 1}
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
    * @Route("/tricks/{trickSlug}", name="single_trick", methods={"GET","HEAD"}, defaults={"trickSlug" = null}, requirements={"trickSlug"="[a-z0-9\-]*"})
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
   
    /**
    * @Route("/tricks/{trickSlug}/edit", name="edit_trick", methods={"POST","GET","HEAD"}, defaults={"trickSlug" = null}, requirements={"trickSlug"="[a-z0-9\-]*"})
    */
   public function editTrick($trickSlug, Request $request, ObjectManager $manager)
   {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $trick = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findOneBy(['slug' => $trickSlug]);

        if(!$trick) {
            throw $this->createNotFoundException('Requested trick slug not found');
        }

        // create form with RegistrationType class
        $form = $this->createForm(TrickType::class, $trick);

        // handle requested data
        $form->handleRequest($request);

        // if form is submitted and valid, store data, log the user and redirect to the account route
        if($form->isSubmitted() && $form->isValid()) {

            // store data into database
            $manager->persist($trick);
            $manager->flush();
            
            // store a flash message into session
            $this->addFlash('success', 'The trick has been updated successfully');

            //redirect to edit_trick route
            return $this->redirectToRoute('edit_trick', ['trickSlug' => $trickSlug]);

        }

        return $this->render('tricks/single.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);

   }
   
    /**
    * @Route("/tricks/{trickSlug}/delete", name="delete_trick", methods={"GET","HEAD"}, defaults={"trickSlug" = null}, requirements={"trickSlug"="[a-z0-9\-]*"})
    */
   public function deleteTrick($trickSlug)
   {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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
   
    /**
    * @Route(
    *   "/tricks/{trickSlug}/edit-media/{mediaId}",
    *   name="edit_media", methods={"POST","GET"},
    *   defaults={"trickSlug" = null, "mediaId" = 0},
    *   requirements={"trickSlug"="[a-z0-9\-]*", "mediaId"="[0-9]+"})
    */
   public function editMedia($trickSlug, $mediaId, Request $request, ObjectManager $manager, SessionInterface $session, PictureHandler $pictureHandler, VideoHandler $videoHandler)
   {
       
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');



        $trick = $this->getDoctrine()
        ->getRepository(Trick::class)
        ->findOneBy(['slug' => $trickSlug]);

        if(!$trick) {
            throw $this->createNotFoundException('Requested trick slug not found');
        }

        // create new media mode
        if($mediaId === 0) {
            $media = new Media();
        } 
        
        // edit media mode
        if($mediaId > 0) {
            $media = $this->getDoctrine()
            ->getRepository(Media::class)
            ->findOneBy(['id' => $mediaId]);
            if(!$media) {
                throw $this->createNotFoundException('Requested media not found');
            }
        }

        // picture form
        $form = $this->createForm(PictureType::class, $media);
        $picture = $pictureHandler->handle($request, $form, $media);

        // video form
        $form = $this->createForm(VideoType::class, $media);
        $video = $videoHandler->handle($request, $form, $media);

        return $this->render('tricks/_edit_media.html.twig', [
            'pictureForm' => $picture->getForm()->createView(),
            'videoForm' => $video->getForm()->createView()
        ]);

   }
   
    /**
    * @Route("/tricks/{trickSlug}/media-list-edit", name="media_list_edit", methods={"GET"}, defaults={"trickSlug" = null}, requirements={"trickSlug"="[a-z0-9\-]*"})
    */
   public function mediaListEdit($trickSlug)
   {
       
        $trick = $this->getDoctrine()
        ->getRepository(Trick::class)
        ->findOneBy(['slug' => $trickSlug]);

        if(!$trick) {
            throw $this->createNotFoundException('Requested trick slug not found');
        }

        return $this->render('tricks/_media_list.html.twig', [
            'trick' => $trick
        ]);

   }

 }
