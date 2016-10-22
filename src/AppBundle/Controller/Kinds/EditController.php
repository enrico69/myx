<?php

namespace AppBundle\Controller\Kinds;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Kind;
use AppBundle\Form\KindType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the edition of kinds
 */
class EditController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for editing one specific kind, 
     * selected by his id
     * 
     * @Route("/kinds/edit/{id}", name="kind_edition")
     * @param Kind $kind is the kind, loaded by the param converter
     * @param Request $request is the request object
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function editAction(Kind $kind, Request $request) {
        
        $form = $this->createForm(KindType::class, $kind, array(
                'action' => $this->generateUrl('kind_edition', array('id' => $kind->getId())),
                'method' => 'POST'
            )
        );
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $this->checkObjectSlug($kind);
            
            $strReturn = $this->getUtils()->checkNameSlugExistence(
                $this->getKindRepository(), $kind);
            
            if($strReturn == "Same") {
                $Response = $this->redirectToRoute('kinds_index', array(), 302);
            } elseif($strReturn == "NoExists") {
                try {
                    $this->openTransaction();
                    $this->getDoctrine()->getManager()->persist($kind);
                    $this->getDoctrine()->getManager()->flush();
                    $this->commitTransaction();
                    $Response = $this->redirectToRoute('kinds_index', array(), 302);
                } catch (Exception $ex) {
                    $this->rollbackTransaction();
                    $this->getLogger()
                    ->error('Error during the author edition'
                    . $ex->getMessage());
                    throw $this->createNotFoundException(
                    $this->getTranslator()->trans('edition.error'));
                }
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
                'kinds/edit.html.twig', 
                array(
                    'form' => $form->createView(),
                    'kind' => $kind
                )
            );
        }
        
        return $Response;
    }
    
}