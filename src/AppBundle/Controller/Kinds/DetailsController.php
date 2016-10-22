<?php

namespace AppBundle\Controller\Kinds;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Kind;
use AppBundle\Entity\Book;

/**
 * This controller handle the search of kinds
 */
class DetailsController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;
    
    /**
     * Handle the request for one specific kind, selected by his slug
     * 
     * @Route("/kinds/{slug}", name="kind_details")
     * @param Kind $kind is the kind, loaded by the param converter
     * @author Eric COURTIAL
     */
    public function detailsAction(Kind $kind, Request $request) {
        
        $intPage = $request->query->get('page', 1);
        if(!filter_var($intPage, FILTER_VALIDATE_INT) || $intPage < 1) {
            $intPage = 1;
        }

        $collectionOfBooks = $kind->getBook()->toArray();
        $arrOfBooks = $this->getUtils()
                ->getElementsOfPage(Book::QTY_BOOKS, $intPage, $collectionOfBooks);
        
        $arrViewParams = array(
            'title' => $kind->getName(),
            'currentPage' => $intPage,
            'qtyOfPages' => $this->getUtils()
                ->calculateNumberOfPagesForPagination(
                    count($collectionOfBooks),
                    Book::QTY_BOOKS
                ),
            'url_page' => 'kind_details',
            'url_complement' => '',
            'slug_uri' => $kind->getSlug()
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