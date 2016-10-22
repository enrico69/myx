<?php

namespace AppBundle\Controller\Formats;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Format;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the deletion of formats
 */
class DeleteController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for the deletion of one specific format, 
     * selected by his id
     * 
     * @Route("/formats/delete/{id}", name="format_deletion")
     * @param Format $format is the format, loaded by the param converter
     * @param Request $request is the request object
     * @method("POST")
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function deleteAction(Format $format, Request $request) {
        
        // Checking the csrf
        $this->checkCsrfToken($request, 'formatDeleteForm');

        // Check if the format has some books
        try {
            $arrOfBooks = $this->getBookRepository()->findByFormat( $format->getId(), 1);
            unset($arrOfBooks);
            
            // The format is used. It cannot be deleted...
            $Response = $this->displayGenericMessage('Ooouups', 
                $this->getTranslator()->trans("used.by.books")
            );
        } catch (NoResultException $notFoundException) {
            unset($notFoundException);
            // delete the format and prepare redirection
            $this->openTransaction();
            $this->getDoctrine()->getManager()->remove($format);
            $this->getDoctrine()->getManager()->flush();
            $this->commitTransaction();
            $Response = $this->redirectToRoute('formats_index', array(), 302);
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->error('Error during the research of the books of a format for deletion'
                        . $ex->getMessage());
            throw $this->createNotFoundException(
                $this->getTranslator()->trans('format.deletionError')
            );
        }

        return $Response;
    }
    
}