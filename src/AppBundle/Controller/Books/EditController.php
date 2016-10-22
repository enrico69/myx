<?php

namespace AppBundle\Controller\Books;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Book;
use AppBundle\Form\BookType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the edition of books
 */
class EditController extends Controller {
   
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;
    
    /**
     * Handle the request for registering one book, 
     * selected by his id
     * 
     * @Route("/book/edit/{id}", name="book_edition")
     * @param Book $book is the book, loaded by the param converter
     * @param Request $request is the request object
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function editAction(Book $book, Request $request)
    {
        
        $form = $this->createForm(BookType::class, $book, array(
                'locale' => $request->getLocale(),
                'action' => $this->generateUrl('book_edition', array('id' => $book->getId())),
                'method' => 'POST'
            )
        );
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            
            $blnFound = false;     
            try {
                $bookBis = $this->getBookRepository()->findBySlug($book->getSlug());
                if(count($bookBis) == 1) {
                    if($bookBis[0]->getId() != $book->getId()) {
                        $blnFound = true;
                        $strField = 'slug';
                    }
                } elseif(count($bookBis) > 0) {
                    $blnFound = true;
                    $strField = 'slug';
                }
            } catch (\Exception $ex) {
                $this->getLogger()
                    ->error('Error during the research of a book'
                            . $ex->getMessage());
                throw $this->createNotFoundException(
                    $this->getTranslator()->trans('SearchError')
                );
            }
            
            // According to the ISBN convention, two books on the same support
            // can't have the same ISBN number. For example, the paper edition
            // and the ebook edition will have a different ISBN.
            // However, what happens if somebody scan the paper edition?
            if(!$blnFound) {
                $book->setIsbn(trim($book->getIsbn()));
                if($book->getIsbn() != "") {
                    if($this->checkISBN($book->getIsbn(), $book->getMaterial()->getId(), $book->getId())) {
                        $blnFound = true;
                        $strField = 'ISBN';
                    }
                }
            }
            
            // If there is no other book with the same slug
            if(!$blnFound) { 
                try {
                    $this->openTransaction();
                    
                    $this->getLocationRepository()->removeByBookId($book->getId());
                    $this->getAuthorRepository()->removeByBookId($book->getId());
                    $this->getKindRepository()->removeByBookId($book->getId());
                    
                   foreach ($book->getLocation() as $location) {
                        $location->addBook($book);
                        $this->getDoctrine()->getManager()->persist($location);
                    }

                    foreach ($book->getAuthor() as $author) {
                        $author->addBook($book);
                        $this->getDoctrine()->getManager()->persist($author);
                    }
                    
                    foreach ($book->getKind() as $kind) {
                        $kind->addBook($book);
                        $this->getDoctrine()->getManager()->persist($kind);
                    }
                    
                    $book->setLastUser($this->getUser());
                    $book->setLastModified(new \DateTime('now'));
                    
                    $this->getDoctrine()->getManager()->persist($book);
                    $this->getDoctrine()->getManager()->flush();
                    $this->commitTransaction();
                    $Response = $this->redirectToRoute('books_index', array(), 302);
                    
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
                        array('%field%' => $strField)
                    )
                );
            }
        } else {
            $Response = $this->render(
                'books/edit.html.twig', 
                array(
                    'form' => $form->createView(),
                    'book' => $book
                )
            );
        }
        
        return $Response;
    }

}