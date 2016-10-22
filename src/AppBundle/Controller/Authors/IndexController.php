<?php

namespace AppBundle\Controller\Authors;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Author;

/**
 * This controller handle the search of authors
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

        $strTitle = $this->getTranslator()->transchoice('Author', 2);
        $authorBar = $this->renderView('authors/authors-bar.html.twig');
        
        if (count($arrResults) > 0) { // If results

        $Response = $this->render(
            'pagination/paginationContainer.html.twig', array(
                'title' => $strTitle,
                'subview' => $authorBar,
                'data' => $this->renderView('authors/listing.html.twig',
                    array('authors' => $arrResults)
                ),
                'currentPage' => $arrRequestData['Page'],
                'qtyOfPages' => $this->getUtils()
                    ->calculateNumberOfPagesForPagination(
                        count($arrResults),
                        Author::QTY_AUTHORS
                    ),
                'url_page' => 'authors_index',
                'url_complement' => ''
                )
        );
        } else { // If no results
           $Response = $this->render(
                'pagination/paginationContainer.html.twig', array(
                    'title' => $strTitle,
                    'subview' => $authorBar
                )
            ); 
        }
        
        return $Response;
    }

    
    /**
     * 
     * The method called by the authors index page.
     * 
     * @return the view with the results.
     * @author Eric COURTIAL.
     * @Route("/authors", name="authors_index")
     */
    public function getAuthorsListAction(Request $request) {

        try {
            $intPage = $request->query->get('page', 1);
            if(!filter_var($intPage, FILTER_VALIDATE_INT) || $intPage < 1) {
                $intPage = 1;
            }
            $arrResults = $this->getAuthorRepository()->findAllOrderedBySurname($intPage);
            $arrRequestData = array('Page' => $intPage);
            $Response = $this->getResultsTemplate($arrResults, $arrRequestData);
        } catch (NoResultException $notFoundException) {
            unset($notFoundException);
            $arrResults = array();
            $Response = $this->getResultsTemplate($arrResults);
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->error('Error during the listing of the authors'
                        . $ex->getMessage());
            $Response = $this->displayGenericMessage(
                $this->getTranslator()->trans('SearchError'),
                $this->getTranslator()->trans('errorListingAuthors')
            );
        }
        
        return $Response;
    }
    
}