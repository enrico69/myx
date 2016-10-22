<?php

namespace AppBundle\Controller\Authors;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Author;
use AppBundle\Form\AuthorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the edition of authors
 */
class EditController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;

    /**
     * Handle the request for editing one specific author, 
     * selected by his id
     * 
     * @Route("/authors/edit/{id}", name="author_edition")
     * @param Author $author is the author, loaded by the param converter
     * @param Request $request is the request object
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function editAction(Author $author, Request $request)
    {
        
        $form = $this->createForm(AuthorType::class, $author, array(
                'action' => $this->generateUrl('author_edition', array('id' => $author->getId())),
                'method' => 'POST'
            )
        );
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            
            $blnFound = false;     
            try {
                $authorBis = $this->getAuthorRepository()->findBySlug($author->getSlug());
                if(count($authorBis) == 1) {
                    if($authorBis[0]->getId() != $author->getId()) {
                        $blnFound = true;
                    }
                } elseif(count($authorBis) > 0) {
                    $blnFound = true;
                }
            } catch (\Exception $ex) {
                $this->getLogger()
                    ->error('Error during the research of an author'
                            . $ex->getMessage());
                throw $this->createNotFoundException(
                    $this->getTranslator()->trans('SearchError')
                );
            }
            
            if(!$blnFound) { 
                try {
                    $this->openTransaction();
                    $this->getDoctrine()->getManager()->persist($author);
                    $this->getDoctrine()->getManager()->flush();
                    $this->commitTransaction();
                    $Response = $this->redirectToRoute('authors_index', array(), 302);
                } catch (Exception $e) {
                    $this->rollbackTransaction();
                    throw $this->createNotFoundException(
                        $this->getTranslator()->trans('edition.error')
                    );
                }
            } else {
                $Response = $this->displayGenericMessage(
                    $this->getTranslator()->trans('Error'),
                    $this->getTranslator()->trans(
                        'field.already.exists', 
                        array('%field%' => 'slug')
                    )
                );
            }
        } else {
            $Response = $this->render(
                'authors/edit.html.twig', 
                array(
                    'form' => $form->createView(),
                    'author' => $author
                )
            );
        }
        
        return $Response;
    }
    
}