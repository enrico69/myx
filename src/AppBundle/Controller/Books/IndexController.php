<?php

namespace AppBundle\Controller\Books;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Book;

/**
 * This controller handle the search of editors
 */
class IndexController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Generate the template for the results page.
     * 
     * @param array $arrResults contains the results of the query
     * @param type $arrRequestData contains the criteria of the query
     * @return the view
     * @author Eric COURTIAL
     */
    private function getResultsTemplate($arrResults, $arrRequestData = array()) {

        $strTitle = $this->getTranslator()->transchoice('Book', 2);
        $editorsBar = $this->renderView('books/books-bar.html.twig');
        
        if (count($arrResults) > 0) { // If results

            $Response = $this->render(
                'pagination/paginationContainer.html.twig', array(
                    'title' => $strTitle,
                    'subview' => $editorsBar,
                    'data' => $this->renderView('books/listing.html.twig',
                        array('books' => $arrResults)
                    ),
                    'currentPage' => $arrRequestData['Page'],
                    'qtyOfPages' => $this->getUtils()
                        ->calculateNumberOfPagesForPagination(
                            count($arrResults),
                            Book::QTY_BOOKS
                        ),
                    'url_page' => 'books_index',
                    'url_complement' => ''
                    )
            );
        } else { // If no results
           $Response = $this->render(
                'pagination/paginationContainer.html.twig', array(
                    'title' => $strTitle,
                    'subview' => $editorsBar
                )
            ); 
        }
        
        return $Response;
    }

    
    /**
     * 
     * The method called by the editors index page.
     * 
     * @return the view with the results.
     * @author Eric COURTIAL.
     * @Route("/books", name="books_index")
     */
    public function getBooksListAction(Request $request) {

        try {
            $intPage = $request->query->get('page', 1);
            if(!filter_var($intPage, FILTER_VALIDATE_INT) || $intPage < 1) {
                $intPage = 1;
            }
            $arrResults = $this->getBookRepository()->findAllOrderedByTitle($intPage);
            $arrRequestData = array('Page' => $intPage);
            $Response = $this->getResultsTemplate($arrResults, $arrRequestData);
        } catch (NoResultException $notFoundException) {
            unset($notFoundException);
            $arrResults = array();
            $Response = $this->getResultsTemplate($arrResults);
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->error('Error during the listing of the books index section'
                        . $ex->getMessage());
            $Response = $this->displayGenericMessage(
                $this->getTranslator()->trans('SearchError'),
                $this->getTranslator()->trans('errorListingBooks')
            );
        }
        
        return $Response;
    }
    
}