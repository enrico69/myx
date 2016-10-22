<?php

namespace AppBundle\Controller\Locations;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Location;
use AppBundle\Entity\Book;

/**
 * This controller handle the search of locations
 */
class DetailsController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;
    
    /**
     * Handle the request for one specific location, selected by his slug
     * 
     * @Route("/locations/{slug}", name="location_details")
     * @param Location $location is the location, loaded by the param converter
     * @author Eric COURTIAL
     */
    public function detailsAction(Location $location, Request $request) {
        
        $intPage = $request->query->get('page', 1);
        if(!filter_var($intPage, FILTER_VALIDATE_INT) || $intPage < 1) {
            $intPage = 1;
        }

        $collectionOfBooks = $location->getBook()->toArray();
        $arrOfBooks = $this->getUtils()
                ->getElementsOfPage(Book::QTY_BOOKS, $intPage, $collectionOfBooks);
        
        $arrViewParams = array(
            'title' => $location->getName(),
            'currentPage' => $intPage,
            'qtyOfPages' => $this->getUtils()
                ->calculateNumberOfPagesForPagination(
                    count($collectionOfBooks),
                    Book::QTY_BOOKS
                ),
            'url_page' => 'location_details',
            'url_complement' => '',
            'slug_uri' => $location->getSlug()
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