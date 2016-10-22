<?php

namespace AppBundle\Controller\Books;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Book;

class DetailsController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;
    
    /**
     * Handle the request for one specific book, selected by his slug
     * 
     * @Route("/books/{slug}", name="book_details")
     * @param Book $book is the book, loader by the param converter
     * @author Eric COURTIAL
     */
    public function detailsAction(Book $book) {
        
        $arrDataForTheView = array('book' => $book);
        
        $arrListOfComments = $this->getCommentRepository()->findByBook($book->getId());
        if(count($arrListOfComments) != 0) {
            $arrDataForTheView['comments'] = $arrListOfComments;
        }
        
        $arrListOfNotes = $this->getNoteRepository()->findByBook($book->getId());
        if(count($arrListOfNotes) != 0) {
            $arrDataForTheView['notes'] = $arrListOfNotes;
        }
        
        return $this->render(
            'books/details.html.twig',
            $arrDataForTheView
        );
    }
    
}