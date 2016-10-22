<?php

namespace AppBundle\Controller\Books;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Book;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the deletion of books
 */
class DeleteController extends Controller {
   
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;
    
    /**
     * Handle the request for deleting one book, 
     * selected by his id
     * 
     * @Route("/book/delete/{id}", name="book_deletion")
     * @param Book $book is the book, loaded by the param converter
     * @param Request $request is the request object
     * @method("POST")
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function deleteAction(Book $book, Request $request)
    {
        // Checking the csrf
        $this->checkCsrfToken($request, 'bookDeleteForm');

        try  {
            $this->openTransaction();
            
            $arrComments = $this->getCommentRepository()->findByBook($book->getId());
            foreach ($arrComments as $comment) {
                $this->getDoctrine()->getManager()->remove($comment);
            }
            
            $arrNotes = $this->getNoteRepository()->findByBook($book->getId());
            foreach ($arrNotes as $note) {
                $this->getDoctrine()->getManager()->remove($note);
                $strFileLocation = $this->container->getParameter('kernel.root_dir').'/../web/notes/' . $note->getFilename();
                if(file_exists($strFileLocation)) {
                    unlink($strFileLocation);
                }
            }
            
            $this->getDoctrine()->getManager()->remove($book);
            $this->getDoctrine()->getManager()->flush();
            $this->commitTransaction();
            $Response = $this->redirectToRoute('books_index', array(), 302);
        } catch (Exception $ex) {
            $this->rollbackTransaction();
            $this->getLogger()
                ->error('Error during the research of the books of a book for deletion'
                . $ex->getMessage());
            throw $this->createNotFoundException(
                $this->getTranslator()->trans('book.deletionError'));
        }

        return $Response;
    }

}