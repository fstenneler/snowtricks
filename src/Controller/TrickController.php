<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\Type\CommentType;
use App\Form\Type\TrickType;
use App\Form\Type\VideoType;
use App\Form\Type\PictureType;
use App\Form\Handler\TrickHandler;
use App\Form\Handler\VideoHandler;
use App\Form\Handler\CommentHandler;
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
     * Home page
     * 
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
     * Tricks page
     * 
    * @Route("/category/{categorySlug}", name="tricks", methods={"GET","HEAD"}, defaults={"categorySlug" = "all"}, requirements={"categorySlug"="[a-z0-9\-]*"})
    */
   public function tricks($categorySlug)
   {

       // 404 route for invalid category slugs
       if($categorySlug !== "all") {
           $category = $this->getDoctrine()
                ->getRepository(Category::class)
                ->findOneBySlug($categorySlug);
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
     * Ajax tricks load
     * 
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
     * Ajax media load
     * 
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
   
    /**
     * Ajax edit media form
     * 
    * @Route(
    *   "/tricks/{trickSlug}/edit-media/{action}/{mediaId}",
    *   name="edit_media", methods={"POST","GET"},
    *   defaults={"trickSlug" = null, "action" = "add", "mediaId" = 0},
    *   requirements={"trickSlug"="[a-z0-9\-]*", "action"="^(add|edit|delete|confirm_deletion)", "mediaId"="[0-9]+"})
    */
   public function editMedia(
       $trickSlug,
       $action,
       $mediaId,
       Request $request,
       ObjectManager $manager,
       PictureHandler $pictureHandler,
       VideoHandler $videoHandler
    )
   {
       
        // deny access for unauthenticated users
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $trick = $this->getDoctrine()
        ->getRepository(Trick::class)
        ->findOneBy(['slug' => $trickSlug]);

        if(!$trick) {
            throw $this->createNotFoundException('Requested trick slug not found');
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

        // create new media mode
        if($action === "add") {
            $media = new Media();
            $media->setTrick($trick);
        } 

        // picture form
        $form = $this->createForm(PictureType::class, $media);
        $picture = $pictureHandler->handle($request, $form, $media, $action);

        // video form
        $form = $this->createForm(VideoType::class, $media);
        $video = $videoHandler->handle($request, $form, $media, $action);

        return $this->render('tricks/_edit_media.html.twig', [
            'pictureForm' => $picture->getForm()->createView(),
            'videoForm' => $video->getForm()->createView(),
            'trick' => $trick,
            'media' => $media
        ]);

   }

   
    /**
     * Single trick page
     * 
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
     * Edit trick page
     * 
    * @Route(
    *   "/tricks/{action}/{trickSlug}",
    *   name="edit_trick",
    *   methods={"POST","GET","HEAD"},
    *   defaults={"trickSlug" = "aa", "action" = "add"},
    *   requirements={"trickSlug" = "[a-z0-9\-]*", "action" = "(create|add|edit|delete|confirm_deletion)"})
    */
   public function editTrick($trickSlug, $action, Request $request, ObjectManager $manager, TrickHandler $trickHandler)
   {
        // deny access for unauthenticated users
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $trick = $this->getDoctrine()
        ->getRepository(Trick::class)
        ->findOneBy(['slug' => $trickSlug]);

        // redirect to 404 if trick not found
        if(!$trick && $action !== 'create') {
            throw $this->createNotFoundException('Requested trick slug not found');
        }

        // create new trick mode
        if($action == 'create') {
            $trick = new Trick();
        } 

        // form
        $form = $this->createForm(TrickType::class, $trick);
        $handler = $trickHandler->handle($request, $form, $trick, $this->getUser(), $action);

        // redirection on success
        if($handler->getSuccess()) {

            // if trick deleted or added, redirect to tricks page
            if($action === 'add' || $action === 'confirm_deletion') {
                return $this->redirectToRoute('tricks');
            }

            // if trick created in step 1, redirect to step 2
            if($action === 'create') {
                return $this->redirectToRoute('edit_trick', ['trickSlug' => $trick->getSlug(), 'action' => 'add']);
            }
            return $this->redirectToRoute('edit_trick', ['trickSlug' => $trick->getSlug(), 'action' => $action]);

        }

        return $this->render('tricks/single.html.twig', [
            'trick' => $trick,
            'form' => $handler->getForm()->createView()
        ]);

   }
 
    /**
     * Ajax comments load
     * 
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
     * Ajax add comment form
     * 
    * @Route("/tricks/{trickSlug}/add-comment", name="add_comment", methods={"POST","GET"}, defaults={"trickSlug" = null}, requirements={"trickSlug"="[a-z0-9\-]*"})
    */
    public function addComment($trickSlug, Request $request, ObjectManager $manager, CommentHandler $commentHandler)
    {
    
        // deny access for unauthenticated users
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $trick = $this->getDoctrine()
        ->getRepository(Trick::class)
        ->findOneBy(['slug' => $trickSlug]);

        if(!$trick) {
            throw $this->createNotFoundException('Requested trick slug not found');
        }

        $comment = new Comment();
        $comment->setTrick($trick);
        $comment->setUser($this->getUser());

        $form = $this->createForm(CommentType::class, $comment);
        $handler = $commentHandler->handle($request, $form, $comment);

        return $this->render('tricks/_add_comment.html.twig', [
            'form' => $handler->getForm()->createView()
        ]);

    }

}
