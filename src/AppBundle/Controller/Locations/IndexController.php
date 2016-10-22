<?php

namespace AppBundle\Controller\Locations;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Location;

/**
 * This controller handle the search of locations
 */
class IndexController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Generate the template for the results page.
     * 
     * @param array $arrResults contains the results of the query
     * @param location $arrRequestData contains the criteria of the query
     * @return the view
     * @author Eric COURTIAL
     */
    private function getResultsTemplate($arrResults, $arrRequestData = array()) {

        $strTitle = $this->getTranslator()->transchoice('Location', 2);
        $locationBar = $this->renderView('locations/locations-bar.html.twig');
        
        if (count($arrResults) > 0) { // If results

        $Response = $this->render(
            'pagination/paginationContainer.html.twig', array(
                'title' => $strTitle,
                'subview' => $locationBar,
                'data' => $this->renderView('locations/listing.html.twig',
                    array('locations' => $arrResults)
                ),
                'currentPage' => $arrRequestData['Page'],
                'qtyOfPages' => $this->getUtils()
                    ->calculateNumberOfPagesForPagination(
                        count($arrResults),
                        Location::QTY_LOCATIONS
                    ),
                'url_page' => 'locations_index',
                'url_complement' => ''
                )
        );
        } else { // If no results
           $Response = $this->render(
                'pagination/paginationContainer.html.twig', array(
                    'title' => $strTitle,
                    'subview' => $locationBar
                )
            ); 
        }
        
        return $Response;
    }

    
    /**
     * 
     * The method called by the locations index page.
     * 
     * @return the view with the results.
     * @author Eric COURTIAL.
     * @Route("/locations", name="locations_index")
     */
    public function getLocationsListAction(Request $request) {

        try {
            $intPage = $request->query->get('page', 1);
            if(!filter_var($intPage, FILTER_VALIDATE_INT) || $intPage < 1) {
                $intPage = 1;
            }
            $arrResults = $this->getLocationRepository()->findAllOrderedByName($intPage);
            $arrRequestData = array('Page' => $intPage);
            $Response = $this->getResultsTemplate($arrResults, $arrRequestData);
        } catch (NoResultException $notFoundException) {
            unset($notFoundException);
            $arrResults = array();
            $Response = $this->getResultsTemplate($arrResults);
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->error('Error during the listing of the locations'
                        . $ex->getMessage());
            $Response = $this->displayGenericMessage(
                $this->getTranslator()->trans('SearchError'),
                $this->getTranslator()->trans('errorListingLocations')
            );
        }
        
        return $Response;
    }
    
}