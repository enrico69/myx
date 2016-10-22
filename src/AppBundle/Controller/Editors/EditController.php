<?php

namespace AppBundle\Controller\Editors;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Editor;
use AppBundle\Form\EditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the edition of editors
 */
class EditController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for editing one specific editor, 
     * selected by his id
     * 
     * @Route("/editors/edit/{id}", name="editor_edition")
     * @param Editor $editor is the editor, loaded by the param converter
     * @param Request $request is the request object
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function editAction(Editor $editor, Request $request) {
        
        $form = $this->createForm(EditorType::class, $editor, array(
                'action' => $this->generateUrl('editor_edition', array('id' => $editor->getId())),
                'method' => 'POST'
            )
        );
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $this->checkObjectSlug($editor);
            
            $strReturn = $this->getUtils()->checkNameSlugExistence(
                $this->getEditorRepository(), $editor);
            
            if($strReturn == "Same") {
                $Response = $this->redirectToRoute('editors_index', array(), 302);
            } elseif($strReturn == "NoExists") {
                try {
                    $this->openTransaction();
                    $this->getDoctrine()->getManager()->persist($editor);
                    $this->getDoctrine()->getManager()->flush();
                    $this->commitTransaction();
                    $Response = $this->redirectToRoute('editors_index', array(), 302);
                } catch (Exception $ex) {
                    $this->rollbackTransaction();
                    $this->getLogger()
                    ->error('Error during the editor edition'
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
                'editors/edit.html.twig', 
                array(
                    'form' => $form->createView(),
                    'editor' => $editor
                )
            );
        }
        
        return $Response;
    }
    
}