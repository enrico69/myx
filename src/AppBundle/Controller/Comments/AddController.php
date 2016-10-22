<?php

namespace AppBundle\Controller\Comments;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the addition of comments
 */
class AddController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;
    
    private $theBook;
    private $selectedBookId;
    
    private function checkBookId(Request $request) {
        
        if($request->getMethod() == "GET") {
            // Extracting the book
            $this->selectedBookId = $request->query->get('bookid', '');
        } else {
            $this->selectedBookId = "";
            if(array_key_exists('bookId', $_POST['comment'])) {
                $this->selectedBookId = $_POST['comment']['bookId'];
            }
        }
        
        if(filter_var($this->selectedBookId, FILTER_VALIDATE_INT)) { // Check book Id
            $this->theBook = $this->getBookRepository()->find($this->selectedBookId);
        }

        if(is_null($this->theBook)) {
            throw $this->createNotFoundException(
                $this->getTranslator()->trans('book.NotFound')
            );
        }
        
    }

    /**
     * Handle the request for registering one comment, 
     * selected by his id
     * 
     * @Route("/comments/add", name="comment_addition")
     * @param Request $request is the request object
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function addAction(Request $request) 
    {

        $comment = new Comment();
        $this->checkBookId($request);
        
        $form = $this->createForm(CommentType::class, $comment, array(
                'action' => $this->generateUrl('comment_addition'),
                'bookId' => $this->selectedBookId,
                'method' => 'POST'
            )
        );
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            try {
                $this->openTransaction();
                $comment->setBook($this->theBook);
                $comment->setUser($this->getUser());
                $this->getDoctrine()->getManager()->persist($comment);
                $this->getDoctrine()->getManager()->flush();
                $this->commitTransaction();
                $Response = $this->redirectToRoute('book_details', array('slug' => $comment->getBook()->getSlug()), 302);
            } catch (\Exception $ex) {
                $this->rollbackTransaction();
                $this->getLogger()
                    ->error('Error during the insertion of a comment'
                            . $ex->getMessage());
                throw $this->createNotFoundException(
                    $this->getTranslator()->trans('insertion.Error')
                );
            }
        } else {
            $Response = $this->render(
                'comments/add.html.twig', 
                array(
                    'form' => $form->createView(),
                    'editor' => $comment,
                    'book' => $this->theBook
                )
            );
        }
        
        return $Response;
    }
    
}