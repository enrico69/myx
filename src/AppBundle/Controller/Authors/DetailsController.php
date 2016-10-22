<?php

namespace AppBundle\Controller\Authors;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Author;
use AppBundle\Entity\Book;

/**
 * This controller handle the search of authors
 */
class DetailsController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;
    
    /**
     * Handle the request for one specific author, selected by his slug
     * 
     * @Route("/authors/{slug}", name="author_details")
     * @param Author $author is the author, loaded by the param converter
     * @author Eric COURTIAL
     */
    public function detailsAction(Author $author, Request $request) {
        
        $intPage = $request->query->get('page', 1);
        if(!filter_var($intPage, FILTER_VALIDATE_INT) || $intPage < 1) {
            $intPage = 1;
        }

        $collectionOfBooks = $author->getBook()->toArray();
        $arrOfBooks = $this->getUtils()
                ->getElementsOfPage(Book::QTY_BOOKS, $intPage, $collectionOfBooks);
        
        $arrViewParams = array(
            'title' => $author->getName() . " " . $author->getSurName(),
            'currentPage' => $intPage,
            'qtyOfPages' => $this->getUtils()
                ->calculateNumberOfPagesForPagination(
                    count($collectionOfBooks),
                    Book::QTY_BOOKS
                ),
            'url_page' => 'author_details',
            'url_complement' => '',
            'slug_uri' => $author->getSlug()
        );
        
        if(!empty($arrOfBooks)) {
            $data = $this->renderView('books/listing.html.twig',
                array('books' => $arrOfBooks)
            );
            $arrViewParams['data'] = $data;
        }
        
        return $this->render('pagination/paginationContainer.html.twig', $arrViewParams);
    }
    
}