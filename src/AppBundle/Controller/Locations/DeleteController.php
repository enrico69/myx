<?php

namespace AppBundle\Controller\Locations;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Location;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the deletion of locations
 */
class DeleteController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for the deletion of one specific location, 
     * selected by his id
     * 
     * @Route("/locations/delete/{id}", name="location_deletion")
     * @param Location $location is the location, loaded by the param converter
     * @param Request $request is the request object
     * @method("POST")
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function deleteAction(Location $location, Request $request) {
        
        // Checking the csrf
        $this->checkCsrfToken($request, 'locationDeleteForm');

        // Check if the location has some books
        if(count($location->getBook()) > 0) {
            // The location is used. It cannot be deleted...
            $Response = $this->displayGenericMessage('Ooouups', 
                $this->getTranslator()->trans("used.by.books")
            ); 
        } else {
            try  {
                $this->getDoctrine()->getManager()->remove($location);
                $this->getDoctrine()->getManager()->flush();
                $Response = $this->redirectToRoute('locations_index', array(), 302);
            } catch (Exception $ex) {
                $this->getLogger()
                    ->error('Error during the research of the books of a location for deletion'
                    . $ex->getMessage());
                throw $this->createNotFoundException(
                    $this->getTranslator()->trans('location.deletionError'));
            }
        }

        return $Response;
    }
    
}