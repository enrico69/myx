<?php

namespace AppBundle\Controller\Editors;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Editor;

/**
 * This controller handle the search of editors
 */
class DetailsController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;
    
    /**
     * Handle the request for one specific editor, selected by his slug
     * 
     * @Route("/editors/{slug}", name="editor_details")
     * @param Editor $editor is the editor, loaded by the param converter
     * @author Eric COURTIAL
     */
    public function detailsAction(Editor $editor, Request $request) {
        
        $intPage = $request->query->get('page', 1);
        if(!filter_var($intPage, FILTER_VALIDATE_INT) || $intPage < 1) {
            $intPage = 1;
        }
        
        try {
            $arrOfBooks = $this->getBookRepository()->findByEditor(
                $editor->getId(),
                $intPage
            );
        } catch (NoResultException $notFoundException) {
            unset($notFoundException);
            $arrOfBooks = null;
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->error('Error during the research of the books of an editor'
                        . $ex->getMessage());
            throw $this->createNotFoundException(
                $this->getTranslator()->trans('SearchError')
            );
        }
        
        $arrViewParams = array(
            'title' => $editor->getName(),
            'currentPage' => $intPage,
            'qtyOfPages' => $this->getUtils()
                ->calculateNumberOfPagesForPagination(
                    count($arrOfBooks),
                    Editor::QTY_EDITORS
                ),
            'url_page' => 'editor_details',
            'url_complement' => '',
            'slug_uri' => $editor->getSlug()
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