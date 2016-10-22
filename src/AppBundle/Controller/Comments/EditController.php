<?php

namespace AppBundle\Controller\Comments;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the edition of comments
 */
class EditController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;
    
    /**
     * Handle the request for editing one specific editor, 
     * selected by his id
     * 
     * @Route("/comment/edition/{id}", name="comment_edition")
     * @param Comment $comment is the editor, loaded by the param converter
     * @param Request $request is the request object
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function editAction(Comment $comment, Request $request) {
        
        $form = $this->createForm(CommentType::class, $comment, array(
                'action' => $this->generateUrl('comment_edition', array('id' => $comment->getId())),
                'method' => 'POST'
            )
        );
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            try {
                $Response = $this->redirectToRoute('book_details', array('slug' => $comment->getBook()->getSlug()), 302);
                if($this->getUser()->getId() == $comment->getUser()->getId()) {
                    $this->openTransaction();
                    $this->getDoctrine()->getManager()->persist($comment);
                    $this->getDoctrine()->getManager()->flush();
                    $this->commitTransaction();
                }
            } catch (Exception $ex) {
                    $this->rollbackTransaction();
                    $this->getLogger()
                    ->error('Error during the comment edition'
                    . $ex->getMessage());
                    throw $this->createNotFoundException(
                    $this->getTranslator()->trans('edition.error'));
            }
        } else {
            $Response = $this->render(
                'comments/edit.html.twig', 
                array(
                    'form' => $form->createView(),
                    'comment' => $comment,
                    'book' => $comment->getBook()
                )
            );
        }
        
        return $Response;
    }
    
}