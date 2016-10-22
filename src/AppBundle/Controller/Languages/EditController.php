<?php

namespace AppBundle\Controller\Languages;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Language;
use AppBundle\Form\LanguageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the edition of languages
 */
class EditController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for editing one specific language, 
     * selected by his id
     * 
     * @Route("/languages/edit/{id}", name="language_edition")
     * @param Language $language is the language, loaded by the param converter
     * @param Request $request is the request object
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function editAction(Language $language, Request $request) {
        
        $form = $this->createForm(LanguageType::class, $language, array(
                'action' => $this->generateUrl('language_edition', array('id' => $language->getId())),
                'method' => 'POST'
            )
        );
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $this->checkObjectSlug($language);
            
            $strReturn = $this->getUtils()->checkNameSlugExistence(
                $this->getLanguageRepository(), $language);
            
            if($strReturn == "Same") {
                $Response = $this->redirectToRoute('languages_index', array(), 302);
            } elseif($strReturn == "NoExists") {
                try {
                    $this->openTransaction();
                    $this->getDoctrine()->getManager()->persist($language);
                    $this->getDoctrine()->getManager()->flush();
                    $this->commitTransaction();
                    $Response = $this->redirectToRoute('languages_index', array(), 302);
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
                'languages/edit.html.twig', 
                array(
                    'form' => $form->createView(),
                    'language' => $language
                )
            );
        }
        
        return $Response;
    }
    
}