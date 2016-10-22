<?php

namespace AppBundle\Controller\Locations;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Location;
use AppBundle\Form\LocationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the edition of locations
 */
class EditController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for editing one specific location, 
     * selected by his id
     * 
     * @Route("/locations/edit/{id}", name="location_edition")
     * @param Location $location is the location, loaded by the param converter
     * @param Request $request is the request object
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function editAction(Location $location, Request $request) {
        
        $form = $this->createForm(LocationType::class, $location, array(
                'action' => $this->generateUrl('location_edition', array('id' => $location->getId())),
                'method' => 'POST'
            )
        );
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $this->checkObjectSlug($location);
            
            $strReturn = $this->getUtils()->checkNameSlugExistence(
                $this->getLocationRepository(), $location);
            
            if($strReturn == "Same") {
                $Response = $this->redirectToRoute('locations_index', array(), 302);
            } elseif($strReturn == "NoExists") {
                $this->getDoctrine()->getManager()->persist($location);
                $this->getDoctrine()->getManager()->flush();
                $Response = $this->redirectToRoute('locations_index', array(), 302);
            } else {
                $Response = $this->displayGenericMessage(
                    $this->getTranslator()->trans('Error'),
                    $this->getTranslator()->trans(
                        'field.already.exists', 
                        array('%field%' => $this->getTranslator()->trans($strReturn))
                    )
                );
            }
        } else {
            $Response = $this->render(
                'locations/edit.html.twig', 
                array(
                    'form' => $form->createView(),
                    'location' => $location
                )
            );
        }
        
        return $Response;
    }
    
}