<?php

namespace AppBundle\Controller\Editors;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Editor;
use AppBundle\Form\EditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the addition of editors
 */
class AddController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for registering one specific editor, 
     * selected by his id
     * 
     * @Route("/editors/add", name="editor_addition")
     * @param Request $request is the request object
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function addAction(Request $request) {
        
        $editor = new Editor();
        
        $form = $this->createForm(EditorType::class, $editor, array(
                'action' => $this->generateUrl('editor_addition'),
                'method' => 'POST'
            )
        );
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $blnFound = false;
            $this->checkObjectSlug($editor);
            try {
                $editorBis = $this->getEditorRepository()->findBySlug($editor->getSlug());
                if(count($editorBis) > 0) {
                    $blnFound = true;
                    $strMessage = 'field.already.exists';
                    $strDuplicateField = "Slug";
                }
                if(!$blnFound) {
                    $editorBis = $this->getEditorRepository()->findByName($editor->getName());
                    if(count($editorBis) > 0) {
                        $blnFound = true;
                        $strMessage = 'field.already.exists';
                        $strDuplicateField = "Name";
                    }
                }
            } catch (\Exception $ex) {
                $this->getLogger()
                    ->error('Error during the research of an editor'
                            . $ex->getMessage());
                throw $this->createNotFoundException(
                    $this->getTranslator()->trans('SearchError')
                );
            }
            
            try{
                if(!$blnFound) {
                    $this->openTransaction();
                    $this->getDoctrine()->getManager()->persist($editor);
                    $this->getDoctrine()->getManager()->flush();
                    $this->commitTransaction();
                    $Response = $this->redirectToRoute('editors_index', array(), 302);
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
                    ->error('Error during the research of an editor'
                            . $ex->getMessage());
                throw $this->createNotFoundException(
                    $this->getTranslator()->trans('genericError')
                );
            }
        } else {
            $Response = $this->render(
                'editors/add.html.twig', 
                array(
                    'form' => $form->createView(),
                    'editor' => $editor
                )
            );
        }
        
        return $Response;
    }
    
}