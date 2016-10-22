<?php

namespace AppBundle\Controller\Languages;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Language;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the deletion of languages
 */
class DeleteController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for the deletion of one specific language, 
     * selected by his id
     * 
     * @Route("/languages/delete/{id}", name="language_deletion")
     * @param Language $language is the language, loaded by the param converter
     * @param Request $request is the request object
     * @method("POST")
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function deleteAction(Language $language, Request $request) {
        
        // Checking the csrf
        $this->checkCsrfToken($request, 'languageDeleteForm');

        // Check if the language has some books
        try {
            $arrOfBooks = $this->getBookRepository()->findByLanguage( $language->getId(), 1);
            unset($arrOfBooks);
            
            // The language is used. It cannot be deleted...
            $Response = $this->displayGenericMessage('Ooouups', 
                $this->getTranslator()->trans("used.by.books")
            );
        } catch (NoResultException $notFoundException) {
            unset($notFoundException);
            // delete the language and prepare redirection
            $this->openTransaction();
            $this->getDoctrine()->getManager()->remove($language);
            $this->getDoctrine()->getManager()->flush();
            $this->commitTransaction();
            $Response = $this->redirectToRoute('languages_index', array(), 302);
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->error('Error during the research of the books of a language for deletion'
                        . $ex->getMessage());
            throw $this->createNotFoundException(
                $this->getTranslator()->trans('language.deletionError')
            );
        }

        return $Response;
    }
    
}