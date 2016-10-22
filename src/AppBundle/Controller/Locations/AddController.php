<?php

namespace AppBundle\Controller\Locations;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Location;
use AppBundle\Form\LocationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the addition of locations
 */
class AddController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for registering one specific location, 
     * selected by his id
     * 
     * @Route("/locations/add", name="location_addition")
     * @param Request $request is the request object
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function addAction(Request $request) {
        
        $location = new Location();
        
        $form = $this->createForm(LocationType::class, $location, array(
                'action' => $this->generateUrl('location_addition'),
                'method' => 'POST'
            )
        );
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $this->checkObjectSlug($location);
            $blnFound = false;     
            try {
                $locationBis = $this->getLocationRepository()->findBySlug($location->getSlug());
                if(count($locationBis) > 0) {
                    $blnFound = true;
                    $strMessage = 'field.already.exists';
                    $strDuplicateField = "Slug";
                }
                if(!$blnFound) {
                    $locationBis = $this->getLocationRepository()->findByName($location->getName());
                    if(count($locationBis) > 0) {
                        $blnFound = true;
                        $strMessage = 'field.already.exists';
                        $strDuplicateField = "Name";
                    }
                }
            } catch (\Exception $ex) {
                $this->getLogger()
                    ->error('Error during the research of a location'
                            . $ex->getMessage());
                throw $this->createNotFoundException(
                    $this->getTranslator()->trans('SearchError')
                );
            }
            
            try {
                if(!$blnFound) {
                    $this->openTransaction();
                    $this->getDoctrine()->getManager()->persist($location);
                    $this->getDoctrine()->getManager()->flush();
                    $this->commitTransaction();
                    $Response = $this->redirectToRoute('locations_index', array(), 302);
                } else {
                    $Response = $this->displayGenericMessage(
                        $this->getTranslator()->trans('Error'),
                        $this->getTranslator()->trans($strMessage,
                            array('%field%' => 
                                $this->getTranslator()
                                ->trans($strDuplicateField)))   
                    );
                }
            } catch (\Exception $ex) {
                $this->rollbackTransaction();
                $this->getLogger()
                    ->error('Error during the research of a format'
                            . $ex->getMessage());
                throw $this->createNotFoundException(
                    $this->getTranslator()->trans('genericError')
                );
            }
        } else {
            $Response = $this->render(
                'locations/add.html.twig', 
                array(
                    'form' => $form->createView(),
                    'location' => $location
                )
            );
        }
        
        return $Response;
    }
    
}