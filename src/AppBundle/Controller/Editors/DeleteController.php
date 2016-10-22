<?php

namespace AppBundle\Controller\Editors;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Editor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the deletion of editors
 */
class DeleteController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for the deletion of one specific editor, 
     * selected by his id
     * 
     * @Route("/editors/delete/{id}", name="editor_deletion")
     * @param Editor $editor is the editor, loaded by the param converter
     * @param Request $request is the request object
     * @method("POST")
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function deleteAction(Editor $editor, Request $request) {
        
        // Checking the csrf
        $this->checkCsrfToken($request, 'editorDeleteForm');

        // Check if the editor has some books
        try {
            $arrOfBooks = $this->getBookRepository()->findByEditor( $editor->getId(), 1);
            unset($arrOfBooks);
            
            // The editors is used. It cannot be deleted...
            $Response = $this->displayGenericMessage('Ooouups', 
                $this->getTranslator()->trans("used.by.books")
            );
        } catch (NoResultException $notFoundException) {
            unset($notFoundException);
            // delete the editor and prepare redirection
            $this->openTransaction();
            $this->getDoctrine()->getManager()->remove($editor);
            $this->getDoctrine()->getManager()->flush();
            $this->commitTransaction();
            $Response = $this->redirectToRoute('editors_index', array(), 302);
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->error('Error during the research of the books of an editor for deletion'
                        . $ex->getMessage());
            throw $this->createNotFoundException(
                $this->getTranslator()->trans('editor.deletionError')
            );
        }

        return $Response;
    }
    
}