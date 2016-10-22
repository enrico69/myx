<?php

namespace AppBundle\Controller\Formats;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Format;
use AppBundle\Form\FormatType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the edition of formats
 */
class EditController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for editing one specific format, 
     * selected by his id
     * 
     * @Route("/formats/edit/{id}", name="format_edition")
     * @param Format $format is the format, loaded by the param converter
     * @param Request $request is the request object
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function editAction(Format $format, Request $request) {
        
        $form = $this->createForm(FormatType::class, $format, array(
                'action' => $this->generateUrl('format_edition', array('id' => $format->getId())),
                'method' => 'POST'
            )
        );
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $this->checkObjectSlug($format);
            
            $strReturn = $this->getUtils()->checkNameSlugExistence(
                $this->getFormatRepository(), $format);
            
            if($strReturn == "Same") {
                $Response = $this->redirectToRoute('formats_index', array(), 302);
            } elseif($strReturn == "NoExists") {
                try {
                    $this->openTransaction();
                    $this->getDoctrine()->getManager()->persist($format);
                    $this->getDoctrine()->getManager()->flush();
                    $Response = $this->redirectToRoute('formats_index', array(), 302);
                    $this->commitTransaction();
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
                'formats/edit.html.twig', 
                array(
                    'form' => $form->createView(),
                    'format' => $format
                )
            );
        }
        
        return $Response;
    }
    
}