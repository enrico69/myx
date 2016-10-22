<?php

namespace AppBundle\Controller\Kinds;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Kind;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the deletion of kinds
 */
class DeleteController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for the deletion of one specific kind, 
     * selected by his id
     * 
     * @Route("/kinds/delete/{id}", name="kind_deletion")
     * @param Kind $kind is the kind, loaded by the param converter
     * @param Request $request is the request object
     * @method("POST")
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function deleteAction(Kind $kind, Request $request) {
        
        // Checking the csrf
        $this->checkCsrfToken($request, 'kindDeleteForm');

        // Check if the kind has some books
        if(count($kind->getBook()) > 0) {
            // The kind is used. It cannot be deleted...
            $Response = $this->displayGenericMessage('Ooouups', 
                $this->getTranslator()->trans("used.by.books")
            ); 
        } else {
            try  {
                $this->openTransaction();
                $this->getDoctrine()->getManager()->remove($kind);
                $this->getDoctrine()->getManager()->flush();
                $this->commitTransaction();
                $Response = $this->redirectToRoute('kinds_index', array(), 302);
            } catch (Exception $ex) {
                $this->getLogger()
                    ->error('Error during the research of the books of a kind for deletion'
                    . $ex->getMessage());
                throw $this->createNotFoundException(
                    $this->getTranslator()->trans('kind.deletionError'));
            }
        }

        return $Response;
    }
    
}