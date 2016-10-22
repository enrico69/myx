<?php

namespace AppBundle\Controller\Books;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Book;
use AppBundle\Form\BookType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the addition of books
 */
class AddController extends Controller {
   
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;
    
    /**
     * Handle the request for registering one book, 
     * selected by his id
     * 
     * @Route("/books/add", name="book_addition")
     * @param Request $request is the request object
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function addAction(Request $request) 
    {

        $book = new Book();
        
        $form = $this->createForm(BookType::class, $book, array(
                'locale' => $request->getLocale(),
                'action' => $this->generateUrl('book_addition'),
                'method' => 'POST'
            )
        );
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            
            $this->checkObjectSlug($book);
            $blnFound = false;     
            try {
                $bookBis = $this->getBookRepository()->findBySlug($book->getSlug());
                if(count($bookBis) > 0) {
                    $blnFound = true;
                    $strMessage = 'field.already.exists';
                    $strDuplicateField = "Slug";
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
                    if($this->checkISBN($book->getIsbn(), $book->getMaterial()->getId())) {
                        $blnFound = true;
                        $strMessage = 'field.already.exists';
                        $strDuplicateField = "Isbn";
                    }
                }
            }
            
            if(!$blnFound) {
            
                try {
                    // Only when creating the object
                    if(is_null($book->getAdditionDate())) {
                        $book->setAdditionDate(new \DateTime('now'));
                    }
                    if(is_null($book->getUser())) {
                        $book->setUser($this->getUser());
                    }

                    // Save/Update the objects
                    $this->openTransaction();
                    $this->getDoctrine()->getManager()->persist($book);
                    
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
                    
                    $this->getDoctrine()->getManager()->flush();
                    $this->commitTransaction();
                    $Response = $this->redirectToRoute('books_index', array(), 302);
                } catch (\Exception $ex) {
                    $this->rollbackTransaction();
                    $this->getLogger()
                        ->error('Error during the insertion of a book'
                                . $ex->getMessage());
                    throw $this->createNotFoundException(
                        $this->getTranslator()->trans('insertion.Error')
                    );
                }
            } else {
                $Response = $this->displayGenericMessage(
                        $this->getTranslator()->trans('Error'),
                        $this->getTranslator()->trans($strMessage,
                            array('%field%' => 
                                $this->getTranslator()
                                ->trans($strDuplicateField)))   
                    );
            }
        } else {
            $Response = $this->render(
                'books/add.html.twig', 
                array(
                    'form' => $form->createView(),
                    'book' => $book
                )
            );
        }
        
        return $Response;
    }
    
}