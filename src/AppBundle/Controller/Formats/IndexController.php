<?php

namespace AppBundle\Controller\Formats;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Format;

/**
 * This controller handle the search of formats
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

        $strTitle = $this->getTranslator()->transchoice('Format', 2);
        $formatBar = $this->renderView('formats/formats-bar.html.twig');
        
        if (count($arrResults) > 0) { // If results

        $Response = $this->render(
            'pagination/paginationContainer.html.twig', array(
                'title' => $strTitle,
                'subview' => $formatBar,
                'data' => $this->renderView('formats/listing.html.twig',
                    array('formats' => $arrResults)
                ),
                'currentPage' => $arrRequestData['Page'],
                'qtyOfPages' => $this->getUtils()
                    ->calculateNumberOfPagesForPagination(
                        count($arrResults),
                        Format::QTY_FORMATS
                    ),
                'url_page' => 'formats_index',
                'url_complement' => ''
                )
        );
        } else { // If no results
           $Response = $this->render(
                'pagination/paginationContainer.html.twig', array(
                    'title' => $strTitle,
                    'subview' => $formatBar
                )
            ); 
        }
        
        return $Response;
    }

    
    /**
     * 
     * The method called by the formats index page.
     * 
     * @return the view with the results.
     * @author Eric COURTIAL.
     * @Route("/formats", name="formats_index")
     */
    public function getFormatsListAction(Request $request) {

        try {
            $intPage = $request->query->get('page', 1);
            if(!filter_var($intPage, FILTER_VALIDATE_INT) || $intPage < 1) {
                $intPage = 1;
            }
            $arrResults = $this->getFormatRepository()->findAllOrderedByName($intPage);
            $arrRequestData = array('Page' => $intPage);
            $Response = $this->getResultsTemplate($arrResults, $arrRequestData);
        } catch (NoResultException $notFoundException) {
            unset($notFoundException);
            $arrResults = array();
            $Response = $this->getResultsTemplate($arrResults);
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->error('Error during the listing of the formats'
                        . $ex->getMessage());
            $Response = $this->displayGenericMessage(
                $this->getTranslator()->trans('SearchError'),
                $this->getTranslator()->trans('errorListingFormats')
            );
        }
        
        return $Response;
    }
    
}