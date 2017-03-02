<?php

namespace AppBundle\Controller\Notes;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Note;
use AppBundle\Form\NoteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * This controller handle the addition of notes
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
            
            if(array_key_exists('bookId', $_POST['note'])) {
                $this->selectedBookId = $_POST['note']['bookId'];
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
     * Handle the request for uploading one note, 
     * selected by his id
     * 
     * @Route("/notes/add", name="note_addition")
     * @param Request $request is the request object
     * @Security("has_role('ROLE_USER')")
     * @author Eric COURTIAL
     */
    public function addAction(Request $request) 
    {

        $note = new Note();
        $this->checkBookId($request);
        
        $form = $this->createForm(NoteType::class, $note, array(
                'action' => $this->generateUrl('note_addition'),
                'bookId' => $this->selectedBookId,
                'method' => 'POST'
            )
        );
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $this->openTransaction();
            
            try {
                // Handling the file
                $file = $note->getFilename();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $fileDir = $this->container->getParameter('kernel.root_dir').'/../web/notes';
                $file->move($fileDir, $fileName);
                $note->setFilename($fileName);
                $note->setBook($this->theBook);
                $note->setUser($this->getUser());
                
                $this->getDoctrine()->getManager()->persist($note);
                $this->getDoctrine()->getManager()->flush();
                $this->commitTransaction();
                $Response = $this->redirectToRoute('book_details', array('slug' => $note->getBook()->getSlug()), 302);
            } catch (\Exception $ex) {
                $this->rollbackTransaction();
                $this->getLogger()
                    ->error('Error during the insertion of a note'
                            . $ex->getMessage());
                throw $this->createNotFoundException(
                    $this->getTranslator()->trans('insertion.Error')
                );
            }
        } else {
            $Response = $this->render(
                'notes/add.html.twig', 
                array(
                    'form' => $form->createView(),
                    'note' => $note,
                    'book' => $this->theBook
                )
            );
        }
        
        return $Response;
    }
    
}