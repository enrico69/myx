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
class DetailsController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;
    
    /**
     * Handle the request for one specific language, selected by his slug
     * 
     * @Route("/languages/{slug}", name="language_details")
     * @param Language $language is the language, loaded by the param converter
     * @author Eric COURTIAL
     */
    public function detailsAction(Language $language, Request $request) {
        
        $intPage = $request->query->get('page', 1);
        if(!filter_var($intPage, FILTER_VALIDATE_INT) || $intPage < 1) {
            $intPage = 1;
        }
        
        try {
            $arrOfBooks = $this->getBookRepository()->findByLanguage(
                $language->getId(),
                $intPage
            );
        } catch (NoResultException $notFoundException) {
            unset($notFoundException);
            $arrOfBooks = null;
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->error('Error during the research of the books of an language'
                        . $ex->getMessage());
            throw $this->createNotFoundException(
                $this->getTranslator()->trans('SearchError')
            );
        }
        
        $arrViewParams = array(
            'title' => $language->getName(),
            'currentPage' => $intPage,
            'qtyOfPages' => $this->getUtils()
                ->calculateNumberOfPagesForPagination(
                    count($arrOfBooks),
                    Language::QTY_LANGUAGES
                ),
            'url_page' => 'language_details',
            'url_complement' => '',
            'slug_uri' => $language->getSlug()
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