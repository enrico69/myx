<?php

namespace AppBundle\Controller\Locations;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Location;

/**
 * This controller handle the search of books by occurences
 */
class SearchController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;
    
    /**
     * Handle the search of locations.
     * 
     * @param array $arrParams
     * @return paginator (array of books)
     * @throws Exception
     * @author Eric COURTIAL
     */
    private function getResults($arrParams) {
        
        try {
            $arrResults = $this->getLocationRepository()
                ->searchByName(
                    $arrParams['Request'],
                    $arrParams['Page']
                );
        } catch (NoResultException $notFoundException) {
            unset($notFoundException);
            throw $this->createNotFoundException(
                $this->getTranslator()->trans('NoResults')
            );
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->error('Error during the research of a location'
                            . $ex->getMessage());
            throw $this->createNotFoundException(
                $this->getTranslator()->trans('SearchError')
            );
        }

        return $arrResults;
        
    }

    /**
     * Generate the template for the results page.
     * 
     * @param array $arrResults contains the results of the query
     * @param location $arrRequestData contains the criteria of the query
     * @return the view
     * @author Eric COURTIAL
     */
    private function getResultsTemplate($arrResults, $arrRequestData) {

        $locationsBar = $this->renderView('locations/locations-bar.html.twig');
        
        $strSubtitle = $this->getTranslator()->trans(
            'research.subtitle', 
            array(
                '%request%' => $arrRequestData['Request'],
                '%type%' => $this->getTranslator()->trans('Name'),
            )
        );
        
        $strUrlComplement = "&keyword=" . $arrRequestData['Request'];

        return $this->render(
            'pagination/paginationContainer.html.twig', array(
                'title' => $this->getTranslator()->trans('search.results'),
                'subtitle' => $strSubtitle,
                'data' => $this->renderView('locations/listing.html.twig',
                    array('locations' => $arrResults)
                ),
                'currentPage' => $arrRequestData['Page'],
                'qtyOfPages' => $this->getUtils()
                    ->calculateNumberOfPagesForPagination(
                        count($arrResults),
                        Location::QTY_LOCATIONS
                    ),
                'url_page' => 'location_search_results',
                'url_complement' => $strUrlComplement,
                'subview' => $locationsBar
                )
        );
    }
    
    /**
     * 
     * Handle the reception of the parameters of the request.
     * 
     * @param Request $request
     * @return array
     * @throws Not found exception
     * @author Eric COURTIAL
     */
    private function getRequestData(Request $request) {
        
        $strRequest = $request->query->get('keyword');
        if(mb_strlen(trim($strRequest)) == 0) {
            throw $this->createNotFoundException(
                $this->getTranslator()->trans('MissingKeywordSearch')
            );
        }
        
        $intPage = $request->query->get('page', 1);
        if(!filter_var($intPage, FILTER_VALIDATE_INT) || $intPage < 1) {
            $intPage = 1;
        }
        
        return(array(
           'Request' => $strRequest,
           'Page' => $intPage
        ));
    }
    
    /**
     * 
     * The method called by the search form.
     * 
     * @return the view with the results.
     * @author Eric COURTIAL.
     * @Route("/search/locations/results", name="location_search_results")
     */
    public function getResultsAction(Request $request) {
        
        $arrRequestData = $this->getRequestData($request);
        
        return $this->getResultsTemplate(
            $this->getResults($arrRequestData),
            $arrRequestData
        );

    }
    
}