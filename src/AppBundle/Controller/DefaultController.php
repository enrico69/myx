<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;

class DefaultController extends Controller {
    
    // Let's use the shortcuts.
    use \AppBundle\Utils\Traits\Shortcuts;
    
    /**
     * Return the generated template for the last books widget when
     * there is a message to display instead of the usual list of books.
     * 
     * @param string $strCodeMessage : the code of the message to display
     * @param string $strOptMsg : an optionnal message
     * @return a view
     * @author Eric COURTIAL
     */
    private function getBooksMessage($strCodeMessage, $strOptMsg="") {
        
        return $this->renderView(
            'home/lastBooksMessage.html.twig', array(
                'message' => $this->getTranslator()
                ->trans($strCodeMessage) . "<br/>" .  $strOptMsg
            )
        );
    }
    
    /**
     * Return the generated template for the lasts books widget.
     * @return a View.
     * @author Eric COURTIAL.
     */
    private function getLastBooksTemplate() {
        
        try {
            
            // Find the last registered books
            $lastBooks = $this->getBookRepository()->findLast(5);
            $theView = $this->renderView('home/lastBooks.html.twig', array(
                'theBooks' => $lastBooks
            ));
            
        } catch (NoResultException $notFoundException) {
            unset($notFoundException);
            // If there is no book registered
            $theView = $this->getBooksMessage('no.book.found');
        } catch (\Exception $exception) {
            // If a problem occured
            // In dev: display the complete message
            $strMessage = "";
            if($this->getUtils()->isDevEnvironment()) {
                $strMessage = $exception->getMessage();
            } else { // else: write it in log.
                $this->getLogger()
                    ->error('Error during the extraction of the last books'
                    . $exception->getMessage());
            }
            $theView = $theView = $this->getBooksMessage('load.last.books.failed'
                , $strMessage);
        }
        
        return $theView;
        
    }
    
    /**
     * Handle the generation of the string showing how many books
     * are registered in the database.
     * @author Eric COURTIAL
     * @return the string to display
     */
    private function getBooksQuantity() {
        try {
            $qty = $this->getBookRepository()->countAllBooks();
            $strQty = $this->getTranslator()->transchoice('subwelcome.message',
                $qty,
                array(
                    '%qty%' => $qty
                )
            );
        } catch (\Exception $ex) {
            $strQty = $this->getTranslator()->trans("error.count.qty.books");
            
            // In dev: display the complete message
            if($this->getUtils()->isDevEnvironment()) {
                $strQty .= "<br/>" . $ex->getMessage();
            } else { // else: write it in log.
                $this->getLogger()
                    ->error('Error during counting the qty of books');
                $this->getLogger()
                    ->error($ex->getMessage());
            }
        }
        
        return $strQty;
    }
    
    /**
     * Return the generated template for the top locations widget when
     * there is a message to display instead of the usual list of locations.
     * 
     * @param string $strCodeMessage : the code of the message to display
     * @param string $strOptMsg : an optionnal message
     * @return a view
     * @author Eric COURTIAL
     */
    private function getLocationsMessage($strCodeMessage, $strOptMsg="") {
        
        return $this->renderView(
            'home/topLocationsMessage.html.twig', array(
                'message' => $this->getTranslator()
                ->trans($strCodeMessage) . "<br/>" .  $strOptMsg
            )
        );
    }
    
    private function getTopLocationsTemplate() {
        
        try {
            $arrResults = $this->getLocationRepository()->findByTopLocations(5);
            $theView = $this->renderView('home/topLocations.html.twig', array(
                'theLocations' => $arrResults
            ));
        }catch (NoResultException $notFoundException) {
            unset($notFoundException);
            // If there is no location registered
            $theView = $this->getLocationsMessage('no.location.found');    
        } catch (\Exception $exception) {
            // If a problem occured
            $strMessage = "";
            // In dev: display the complete message
            if($this->getUtils()->isDevEnvironment()) {
                $strMessage = $exception->getMessage();
            } else { // else: write it in log.
                $this->getLogger()
                    ->error('Error during the extraction of the top locations'
                    . ": " . $exception->getMessage());
            }
            
            $theView = $this->getLocationsMessage('load.top.locations.failed', 
                $strMessage);
        }
        
        return $theView;
        
    }
    
    /**
     * Return the generated template for the top contributors widget when
     * there is a message to display instead of the usual list of contributors.
     * 
     * @param string $strCodeMessage : the code of the message to display
     * @param string $strOptMsg : an optionnal message
     * @return a view
     * @author Eric COURTIAL
     */
    private function getContributorsMessage($strCodeMessage, $strOptMsg="") {
        
        return $this->renderView(
            'home/topContributorsMessage.html.twig', array(
                'message' => $this->getTranslator()
                ->trans($strCodeMessage) . "<br/>" .  $strOptMsg
            )
        );
    }
    
    /**
     * Return the generated template for the top contributors widget.
     * @return a View.
     * @author Eric COURTIAL.
     */
    private function getTopContributorsTemplate() {
        
        try {
            $arrResults = $this->getUserRepository()->findByTopContributors(5);
            $theView = $this->renderView('home/topContributors.html.twig', array(
                'theContributors' => $arrResults
            ));
        }catch (NoResultException $notFoundException) {
            unset($notFoundException);
            // If there is no contributors registered
            $theView = $this->getContributorsMessage('no.contributors.found');    
        } catch (\Exception $exception) {
            // If a problem occured
            $strMessage = "";
            // In dev: display the complete message
            if($this->getUtils()->isDevEnvironment()) {
                $strMessage = $exception->getMessage();
            } else { // else: write it in log.
                $this->getLogger()
                    ->error('Error during the extraction of the top contributors'
                    . ": " . $exception->getMessage());
            }
            
            $theView = $this->getContributorsMessage('load.top.contributors.failed', 
                $strMessage);
        }
        
        return $theView;
    }
    
    /**
     * Return the generated template for the top authors widget when
     * there is a message to display instead of the usual list of authors.
     * 
     * @param string $strCodeMessage : the code of the message to display
     * @param string $strOptMsg : an optionnal message
     * @return a view
     * @author Eric COURTIAL
     */
    private function getAuthorsMessage($strCodeMessage, $strOptMsg="") {
        
        return $this->renderView(
            'home/topAuthorsMessage.html.twig', array(
                'message' => $this->getTranslator()
                ->trans($strCodeMessage) . "<br/>" .  $strOptMsg
            )
        );
    }
    
    /**
     * Return the generated template for the top authors widget.
     * @return a View.
     * @author Eric COURTIAL.
     */
    private function getTopAuthorsTemplate() {
        
        try {
            $arrResults = $this->getAuthorRepository()->findByTopAuthors(5);
            $theView = $this->renderView('home/topAuthors.html.twig', array(
                'theAuthors' => $arrResults
            ));
        }catch (NoResultException $notFoundException) {
            unset($notFoundException);
            // If there is no contributors registered
            $theView = $this->getAuthorsMessage('no.authors.found');    
        } catch (\Exception $exception) {
            // If a problem occured
            $strMessage = "";
            // In dev: display the complete message
            if($this->getUtils()->isDevEnvironment()) {
                $strMessage = $exception->getMessage();
            } else { // else: write it in log.
                $this->getLogger()
                    ->error('Error during the extraction of the top authors'
                    . ": " . $exception->getMessage());
            }
            
            $theView = $this->getAuthorsMessage('load.top.authors.failed', 
                $strMessage);
        }
        
        return $theView;
        
    }
     
    /**
     * 
     * The method called by the homepage.
     * 
     * @return the index view.
     * @author Eric COURTIAL.
     * @Route("/", name="homepage")
     */
    public function indexAction() {
        
        // Render the proper view for the last books widget     
        return $this->render('home/index.html.twig', array(
            'quantityMsg' => $this->getBooksQuantity(),
            'lastBooks' => $this->getLastBooksTemplate(),
            'topLocations' => $this->getTopLocationsTemplate(),
            'topContributors' => $this->getTopContributorsTemplate(),
            'topAuthors' => $this->getTopAuthorsTemplate(),
            'welcomeMessage' => $this->container->getParameter('site_welcome_message')
        ));
    }
    
    
}