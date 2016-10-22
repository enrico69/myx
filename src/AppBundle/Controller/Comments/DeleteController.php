<?php

namespace AppBundle\Controller\Comments;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the deletion of comments
 */
class DeleteController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for the deletion of one specific comment, 
     * selected by his id
     * 
     * @Route("/comment/delete/{id}", name="comment_deletion")
     * @param Comment $comment is the author, loaded by the param converter
     * @param Request $request is the request object
     * @method("POST")
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function deleteAction(Comment $comment, Request $request) {
        
        // Checking the csrf
        $this->checkCsrfToken($request, 'commentDeleteForm');

        try  {
            $Response = $this->redirectToRoute('book_details', array('slug' => $comment->getBook()->getSlug()), 302);
            if($this->getUser()->getId() == $comment->getUser()->getId()) {
                $this->openTransaction();
                $this->getDoctrine()->getManager()->remove($comment);
                $this->getDoctrine()->getManager()->flush();
                $this->commitTransaction();
            }
        } catch (Exception $ex) {
            $this->rollbackTransaction();
            $this->getLogger()
                ->error('Error during the research of the comment for deletion'
                    . $ex->getMessage());
            throw $this->createNotFoundException(
                $this->getTranslator()->trans('comment.deletionError'));
       }

        return $Response;
    }
    
}