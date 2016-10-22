<?php

namespace AppBundle\Controller\Kinds;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Kind;
use AppBundle\Form\KindType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the addition of kinds
 */
class AddController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for registering one specific kind, 
     * selected by his id
     * 
     * @Route("/kinds/add", name="kind_addition")
     * @param Request $request is the request object
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function addAction(Request $request) {
        
        $kind = new Kind();
        
        $form = $this->createForm(KindType::class, $kind, array(
                'action' => $this->generateUrl('kind_addition'),
                'method' => 'POST'
            )
        );
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $this->checkObjectSlug($kind);
            $blnFound = false;     
            try {
                $kindBis = $this->getKindRepository()->findBySlug($kind->getSlug());
                if(count($kindBis) > 0) {
                    $blnFound = true;
                    $strMessage = 'field.already.exists';
                    $strDuplicateField = "Slug";
                }
                if(!$blnFound) {
                    $kindBis = $this->getKindRepository()->findByName($kind->getName());
                    if(count($kindBis) > 0) {
                        $blnFound = true;
                        $strMessage = 'field.already.exists';
                        $strDuplicateField = "Name";
                    }
                }
            } catch (\Exception $ex) {
                $this->getLogger()
                    ->error('Error during the research of a kind'
                            . $ex->getMessage());
                throw $this->createNotFoundException(
                    $this->getTranslator()->trans('SearchError')
                );
            }
            
            try {
                if(!$blnFound) {
                    $this->openTransaction();
                    $this->getDoctrine()->getManager()->persist($kind);
                    $this->getDoctrine()->getManager()->flush();
                    $this->commitTransaction();
                    $Response = $this->redirectToRoute('kinds_index', array(), 302);
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
                'kinds/add.html.twig', 
                array(
                    'form' => $form->createView(),
                    'kind' => $kind
                )
            );
        }
        
        return $Response;
    }
    
}