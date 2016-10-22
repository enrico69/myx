<?php

namespace AppBundle\Controller\Authors;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Author;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the deletion of authors
 */
class DeleteController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for the deletion of one specific author, 
     * selected by his id
     * 
     * @Route("/authors/delete/{id}", name="author_deletion")
     * @param Author $author is the author, loaded by the param converter
     * @param Request $request is the request object
     * @method("POST")
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function deleteAction(Author $author, Request $request) {
        
        // Checking the csrf
        $this->checkCsrfToken($request, 'authorDeleteForm');

        // Check if the author has some books
        if(count($author->getBook()) > 0) {
            // The author is used. It cannot be deleted...
            $Response = $this->displayGenericMessage('Ooouups', 
                $this->getTranslator()->trans("used.by.books")
            ); 
        } else {
            try {
                $this->openTransaction();
                $this->getDoctrine()->getManager()->remove($author);
                $this->getDoctrine()->getManager()->flush();
                $this->commitTransaction();
                $Response = $this->redirectToRoute('authors_index', array(), 302);
            } catch (Exception $ex) {
                $this->rollbackTransaction();
                $this->getLogger()
                    ->error('Error during the research of the books of a author for deletion'
                    . $ex->getMessage());
                throw $this->createNotFoundException(
                    $this->getTranslator()->trans('author.deletionError'));
            }
        }

        return $Response;
    }
    
}