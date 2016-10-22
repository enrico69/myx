<?php
namespace AppBundle\Utils\Traits;

/**
 * This trait contains nothing less than shortcuts.
 * To be used in the controler.
 * @author Eric COURTIAL
 */
trait Shortcuts {
    
    /**
     * Shortcuts to get the service "utils".
     * @author Eric COURTIAL
     * @return the service "app.utils.utils"
     */
    private function getUtils() {
        return $this->get('app.utils.utils');
    }
    
    /**
     * Shortcuts to get the service "logger".
     * @author Eric COURTIAL
     * @return the service "logger"
     */
    private function getLogger() {
        return $this->get('logger');
    }
    
    /**
     * Shortcuts to get the service "translator".
     * @author Eric COURTIAL
     * @return the service "logger"
     */
    private function getTranslator() {
        return $this->get('translator');
    }
    
    /**
     * Get the book repository.
     * @return the book repository class.
     * @author Eric COURTIAL.
     */
    private function getBookRepository() {
        
        return $this->getDoctrine()
            ->getRepository('AppBundle:Book');
    }
    
    /**
     * Get the location repository.
     * @return the location repository class.
     * @author Eric COURTIAL.
     */
    private function getLocationRepository() {
        
        return $this->getDoctrine()
            ->getRepository('AppBundle:Location');
    }
    
    /**
     * Get the users repository.
     * @return the user repository class.
     * @author Eric COURTIAL.
     */
    private function getUserRepository() {
        
        return $this->getDoctrine()
            ->getRepository('AppBundle:User');
    }
    
    /**
     * Get the authors repository.
     * @return the author Author class.
     * @author Eric COURTIAL.
     */
    private function getAuthorRepository() {
        
        return $this->getDoctrine()
            ->getRepository('AppBundle:Author');
    }
    
    /**
     * Get the editors repository.
     * @return the editor repository class.
     * @author Eric COURTIAL.
     */
    private function getEditorRepository() {
        
        return $this->getDoctrine()
            ->getRepository('AppBundle:Editor');
    }
    
    /**
     * Get the formats repository.
     * @return the format repository class.
     * @author Eric COURTIAL.
     */
    private function getFormatRepository() {
        
        return $this->getDoctrine()
            ->getRepository('AppBundle:Format');
    }
    
    /**
     * Get the languages repository.
     * @return the languages repository class.
     * @author Eric COURTIAL.
     */
    private function getLanguageRepository() {
        
        return $this->getDoctrine()
            ->getRepository('AppBundle:Language');
    }
    
    /**
     * Get the kinds repository.
     * @return the kinds repository class.
     * @author Eric COURTIAL.
     */
    private function getKindRepository() {
        
        return $this->getDoctrine()
            ->getRepository('AppBundle:Kind');
    }
    
    /**
     * Get the comments repository.
     * @return the comments repository class.
     * @author Eric COURTIAL.
     */
    private function getCommentRepository() {
        
        return $this->getDoctrine()
            ->getRepository('AppBundle:Comment');
    }
    
    /**
     * Get the notes repository.
     * @return the notes repository class.
     * @author Eric COURTIAL.
     */
    private function getNoteRepository() {
        
        return $this->getDoctrine()
            ->getRepository('AppBundle:Note');
    }
    
    /**
     * 
     * Generate a generic response with a message
     * 
     * @param string $strTitle is the title
     * @param string $strMessage is the message content
     * @param string $strSubtitle is the subtitle
     * @return a response object
     * @author Eric COURTIAL
     */
    private function displayGenericMessage($strTitle, $strMessage, $strSubtitle=null) {
        return $this->render(
            'genericMessage.html.twig',
            array(
                'title' => $strTitle,
                'subtitle' => $strSubtitle,
                'message' => $strMessage
            )
        );
    }
    
    /**
     * Check the CSRF token for a form. If it doesn't match, an Exception is
     * raised
     * 
     * @param Request $request is the request object
     * @param string $strFormName is the form name
     * @throws not found error
     * @author Eric COURTIAL
     */
    private function checkCsrfToken($request, $strFormName) {
        $strToken = $request->request->get('_csrf_token', '');
        if(!$this->isCsrfTokenValid($strFormName, $strToken)) {
            throw $this->createNotFoundException(
                $this->getTranslator()->trans('csrfInactive')
            );
        }
    }
    
    /**
     * Check, and if necessary, sanitize the slug of the object.
     * 
     * @param object $object is the object with the slug to check
     * @throws not found exception
     * @author Eric COURTIAL
     */
    private function checkObjectSlug($object) {
        $slugified = $this->getUtils()->slugify($object->getSlug());
        
        if (mb_strlen($slugified) == 0) {
            throw $this->createNotFoundException(
                    $this->getTranslator()->trans('slugEmpty')
            );
        }
        $object->setSlug($slugified);
    }
    
    /**
     * Open a SQL transaction
     * 
     * @author Eric COURTIAL
     */
    private function openTransaction() {
        $this->getDoctrine()->getEntityManager()->getConnection()->beginTransaction();
    }
    
    /**
     * Commit modifications and close a SQL transaction
     * 
     * @author Eric COURTIAL
     */
    private function commitTransaction() {
        $this->getDoctrine()->getEntityManager()->getConnection()->commit();
    }
    
    /**
     * Cancel modifications and close a SQL transaction
     * 
     * @author Eric COURTIAL
     */
    private function rollbackTransaction() {
       $this->getDoctrine()->getEntityManager()->getConnection()->rollback(); 
    }
    
    /*
     * See target method documentation
     * @author Eric COURTIAL
     */
    private function checkISBN($strISBN, $intType, $bookId) {
        return $this->getUtils()->checkISBN($strISBN, $intType, $bookId);
    }
}