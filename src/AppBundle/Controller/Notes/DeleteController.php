<?php

namespace AppBundle\Controller\Notes;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Note;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the deletion of notes
 */
class DeleteController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for the deletion of one specific note, 
     * selected by his id
     * 
     * @Route("/note/delete/{id}", name="note_deletion")
     * @param Note $note is the author, loaded by the param converter
     * @param Request $request is the request object
     * @method("POST")
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function deleteAction(Note $note, Request $request) {
        
        // Checking the csrf
        $this->checkCsrfToken($request, 'noteDeleteForm');

        try  {
            $Response = $this->redirectToRoute('book_details', array('slug' => $note->getBook()->getSlug()), 302);
            if($this->getUser()->getId() == $note->getUser()->getId()) {
                $strFileLocation = $this->container->getParameter('kernel.root_dir').'/../web/notes/' . $note->getFilename();
                if(file_exists($strFileLocation)) {
                    unlink($strFileLocation);
                }

                $this->openTransaction();
                $this->getDoctrine()->getManager()->remove($note);
                $this->getDoctrine()->getManager()->flush();
                $this->commitTransaction();
            }
        } catch (Exception $ex) {
            $this->rollbackTransaction();
            $this->getLogger()
                ->error('Error during the research of the note for deletion'
                    . $ex->getMessage());
            throw $this->createNotFoundException(
                $this->getTranslator()->trans('note.deletionError'));
       }

        return $Response;
    }
    
}