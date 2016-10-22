<?php

namespace AppBundle\Controller\Languages;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Language;

/**
 * This controller handle the search of languages
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

        $strTitle = $this->getTranslator()->transchoice('Language', 2);
        $languageBar = $this->renderView('languages/languages-bar.html.twig');
        
        if (count($arrResults) > 0) { // If results

        $Response = $this->render(
            'pagination/paginationContainer.html.twig', array(
                'title' => $strTitle,
                'subview' => $languageBar,
                'data' => $this->renderView('languages/listing.html.twig',
                    array('languages' => $arrResults)
                ),
                'currentPage' => $arrRequestData['Page'],
                'qtyOfPages' => $this->getUtils()
                    ->calculateNumberOfPagesForPagination(
                        count($arrResults),
                        Language::QTY_LANGUAGES
                    ),
                'url_page' => 'languages_index',
                'url_complement' => ''
                )
        );
        } else { // If no results
           $Response = $this->render(
                'pagination/paginationContainer.html.twig', array(
                    'title' => $strTitle,
                    'subview' => $languageBar
                )
            ); 
        }
        
        return $Response;
    }

    
    /**
     * 
     * The method called by the languages index page.
     * 
     * @return the view with the results.
     * @author Eric COURTIAL.
     * @Route("/languages", name="languages_index")
     */
    public function getLanguagesListAction(Request $request) {

        try {
            $intPage = $request->query->get('page', 1);
            if(!filter_var($intPage, FILTER_VALIDATE_INT) || $intPage < 1) {
                $intPage = 1;
            }
            $arrResults = $this->getLanguageRepository()->findAllOrderedByName($intPage);
            $arrRequestData = array('Page' => $intPage);
            $Response = $this->getResultsTemplate($arrResults, $arrRequestData);
        } catch (NoResultException $notFoundException) {
            unset($notFoundException);
            $arrResults = array();
            $Response = $this->getResultsTemplate($arrResults);
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->error('Error during the listing of the languages'
                        . $ex->getMessage());
            $Response = $this->displayGenericMessage(
                $this->getTranslator()->trans('SearchError'),
                $this->getTranslator()->trans('errorListingLanguages')
            );
        }
        
        return $Response;
    }
    
}