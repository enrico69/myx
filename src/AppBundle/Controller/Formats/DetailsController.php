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
class DetailsController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;
    
    /**
     * Handle the request for one specific format, selected by his slug
     * 
     * @Route("/formats/{slug}", name="format_details")
     * @param Format $format is the format, loaded by the param converter
     * @author Eric COURTIAL
     */
    public function detailsAction(Format $format, Request $request) {
        
        $intPage = $request->query->get('page', 1);
        if(!filter_var($intPage, FILTER_VALIDATE_INT) || $intPage < 1) {
            $intPage = 1;
        }
        
        try {
            $arrOfBooks = $this->getBookRepository()->findByFormat(
                $format->getId(),
                $intPage
            );
        } catch (NoResultException $notFoundException) {
            unset($notFoundException);
            $arrOfBooks = null;
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->error('Error during the research of the books of an format'
                        . $ex->getMessage());
            throw $this->createNotFoundException(
                $this->getTranslator()->trans('SearchError')
            );
        }
        
        $arrViewParams = array(
            'title' => $format->getName(),
            'currentPage' => $intPage,
            'qtyOfPages' => $this->getUtils()
                ->calculateNumberOfPagesForPagination(
                    count($arrOfBooks),
                    Format::QTY_FORMATS
                ),
            'url_page' => 'format_details',
            'url_complement' => '',
            'slug_uri' => $format->getSlug()
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