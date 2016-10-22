<?php

namespace AppBundle\Controller\Authors;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Author;
use AppBundle\Form\AuthorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the addition of authors
 */
class AddController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for registering one specific author, 
     * selected by his id
     * 
     * @Route("/authors/add", name="author_addition")
     * @param Request $request is the request object
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function addAction(Request $request) {
        
        $author = new Author();
        
        $form = $this->createForm(AuthorType::class, $author, array(
                'action' => $this->generateUrl('author_addition'),
                'method' => 'POST'
            )
        );
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $this->checkObjectSlug($author);
            $blnFound = false;     
            try {
                $authorBis = $this->getAuthorRepository()->findBySlug($author->getSlug());
                if(count($authorBis) > 0) {
                    $blnFound = true;
                    $strMessage = 'field.already.exists';
                    $strDuplicateField = "Slug";
                }
                if(!$blnFound) {
                    $authorBis = $this->getAuthorRepository()->findByName($author->getName());
                    if(count($authorBis) > 0) {
                        $blnFound = true;
                        $strMessage = 'field.already.exists';
                        $strDuplicateField = "Name";
                    }
                }
            } catch (\Exception $ex) {
                $this->getLogger()
                    ->error('Error during the research of a author'
                            . $ex->getMessage());
                throw $this->createNotFoundException(
                    $this->getTranslator()->trans('SearchError')
                );
            }
            
            try {
                if(!$blnFound) {
                    $this->openTransaction();
                    $this->getDoctrine()->getManager()->persist($author);
                    $this->getDoctrine()->getManager()->flush();
                    $this->commitTransaction();
                    $Response = $this->redirectToRoute('authors_index', array(), 302);
                } else {
                    $Response = $this->displayGenericMessage(
                        $this->getTranslator()->trans('Error'),
                        $this->getTranslator()->trans($strMessage,
                            array('%field%' => 
                                $this->getTranslator()
                                ->trans($strDuplicateField)))   
                    );
                }
            }catch (\Exception $ex) {
                $this->rollbackTransaction();
                $this->getLogger()
                    ->error('Error during the insertion of a author'
                            . $ex->getMessage());
                throw $this->createNotFoundException(
                    $this->getTranslator()->trans('genericError')
                );
            }
        } else {
            $Response = $this->render(
                'authors/add.html.twig', 
                array(
                    'form' => $form->createView(),
                    'author' => $author
                )
            );
        }
        
        return $Response;
    }
    
}