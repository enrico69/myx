<?php

namespace AppBundle\Controller\Languages;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Language;
use AppBundle\Form\LanguageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the addition of languages
 */
class AddController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for registering one specific language, 
     * selected by his id
     * 
     * @Route("/languages/add", name="language_addition")
     * @param Request $request is the request object
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function addAction(Request $request) {
        
        $language = new Language();
        
        $form = $this->createForm(LanguageType::class, $language, array(
                'action' => $this->generateUrl('language_addition'),
                'method' => 'POST'
            )
        );
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $this->checkObjectSlug($language);
            $blnFound = false;     
            try {
                $languageBis = $this->getLanguageRepository()->findBySlug($language->getSlug());
                if(count($languageBis) > 0) {
                    $blnFound = true;
                    $strMessage = 'field.already.exists';
                    $strDuplicateField = "Slug";
                }
                if(!$blnFound) {
                    $languageBis = $this->getLanguageRepository()->findByName($language->getName());
                    if(count($languageBis) > 0) {
                        $blnFound = true;
                        $strMessage = 'field.already.exists';
                        $strDuplicateField = "Name";
                    }
                }
            } catch (\Exception $ex) {
                $this->getLogger()
                    ->error('Error during the research of a language'
                            . $ex->getMessage());
                throw $this->createNotFoundException(
                    $this->getTranslator()->trans('SearchError')
                );
            }
            
            try {
                if(!$blnFound) {
                    $this->openTransaction();
                    $this->getDoctrine()->getManager()->persist($language);
                    $this->getDoctrine()->getManager()->flush();
                    $this->commitTransaction();
                    $Response = $this->redirectToRoute('languages_index', array(), 302);
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
                'languages/add.html.twig', 
                array(
                    'form' => $form->createView(),
                    'language' => $language
                )
            );
        }
        
        return $Response;
    }
    
}