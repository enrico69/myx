<?php

namespace AppBundle\Controller\Kinds;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Kind;

/**
 * This controller handle the search of kinds
 */
class IndexController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Generate the template for the results page.
     * 
     * @param array $arrResults contains the results of the query
     * @param kind $arrRequestData contains the criteria of the query
     * @return the view
     * @author Eric COURTIAL
     */
    private function getResultsTemplate($arrResults, $arrRequestData = array()) {

        $strTitle = $this->getTranslator()->transchoice('Kind', 2);
        $kindBar = $this->renderView('kinds/kinds-bar.html.twig');
        
        if (count($arrResults) > 0) { // If results

        $Response = $this->render(
            'pagination/paginationContainer.html.twig', array(
                'title' => $strTitle,
                'subview' => $kindBar,
                'data' => $this->renderView('kinds/listing.html.twig',
                    array('kinds' => $arrResults)
                ),
                'currentPage' => $arrRequestData['Page'],
                'qtyOfPages' => $this->getUtils()
                    ->calculateNumberOfPagesForPagination(
                        count($arrResults),
                        Kind::QTY_KINDS
                    ),
                'url_page' => 'kinds_index',
                'url_complement' => ''
                )
        );
        } else { // If no results
           $Response = $this->render(
                'pagination/paginationContainer.html.twig', array(
                    'title' => $strTitle,
                    'subview' => $kindBar
                )
            ); 
        }
        
        return $Response;
    }

    
    /**
     * 
     * The method called by the kinds index page.
     * 
     * @return the view with the results.
     * @author Eric COURTIAL.
     * @Route("/kinds", name="kinds_index")
     */
    public function getKindsListAction(Request $request) {

        try {
            $intPage = $request->query->get('page', 1);
            if(!filter_var($intPage, FILTER_VALIDATE_INT) || $intPage < 1) {
                $intPage = 1;
            }
            $arrResults = $this->getKindRepository()->findAllOrderedByName($intPage);
            $arrRequestData = array('Page' => $intPage);
            $Response = $this->getResultsTemplate($arrResults, $arrRequestData);
        } catch (NoResultException $notFoundException) {
            unset($notFoundException);
            $arrResults = array();
            $Response = $this->getResultsTemplate($arrResults);
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->error('Error during the listing of the kinds'
                        . $ex->getMessage());
            $Response = $this->displayGenericMessage(
                $this->getTranslator()->trans('SearchError'),
                $this->getTranslator()->trans('errorListingKinds')
            );
        }
        
        return $Response;
    }
    
}