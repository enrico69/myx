<?php

namespace AppBundle\Utils\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * This class is a service containing tools.
 * Note: it is available from the trait "Shortcuts".
 * @author Eric COURTIAL
 */
class Utils {
    
    // Will contain the service container.
    private $container;
    
    /**
     * @author Eric COURTIAL
     * @param Container $container
     */
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    /**
     * Allows to check if we are currently in the dev environment.
     * @author Eric COURTIAL
     * @return boolean
     */
    public function isDevEnvironment() {
        
        $blnStatus = false;
        
        if($this->container->getParameter('kernel.environment') == 'dev') {
            $blnStatus = true;
        }
        
        return $blnStatus;
        
    }
    
    /**
     * 
     * Return the number of pages for pagination
     * 
     * @param type $intQty is the quantity of results
     * @param type $intStep is the number of results to display per page
     * @return int the number of pages
     * @author Eric COURTIAL
     */
    public function calculateNumberOfPagesForPagination($intQty, $intStep) {
        
        $intModulo = $intQty % $intStep;
        if($intModulo == 0) {
            $intQtyPages = $intQty / $intStep;
        } else {
            $intTmpQtyResults = $intQty - $intModulo;
            $intQtyPages = $intTmpQtyResults / $intStep + 1;
        }
        
        return $intQtyPages;
    }
    
    /**
     * Check if an entity of the type Format, Editor or Place
     * already exists with the given Name, Slug or both.
     * 
     * @param type $Repository is the repository of the object
     * @param type $Object is the object to compare
     * @return string :
     * 'Same' => if an record exists but represents the same entity
     * 'Name' => if an record exists with the same name
     * 'Slug' => if an record exists with the same slug
     * 'NoExists' => if nothing such exists
     * @throws type
     * 
     * @author Eric COURTIAL
     */
    public function checkNameSlugExistence($Repository, $Object) {
        $blnFind = false;
        $blnSameNameAndId = false;
        $blnSameSlugAndId = false;
        $strObjectClassName = explode('\\', get_class($Object))[2];
        $strReturn = "";
        
        // Checking the name attribute
        try{
            $Elements = $Repository->findByName($Object->getName());
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->error('Error during the research of a ' . 
                        $strObjectClassName. " " . $ex->getMessage());
            throw $this->createNotFoundException(
                $this->getTranslator()->trans('SearchError')
            );
        }
        
        if(count($Elements) > 1) {
            $this->getLogger()
                    ->error('More than one occurence found for ' . 
                        $strObjectClassName. " " . $ex->getMessage());
            throw $this->createNotFoundException(
                $this->getTranslator()->trans('MultipleFound',
                    array('%field%' => $this->getTranslator()->trans("Name"))
                )
            );
        } elseif(count($Elements) == 1) {
            $Result = $Elements[0];
            if($Result->getId() == $Object->getId()) {
                $blnSameNameAndId = true;
            } else {
                $strReturn = "Name";
                $blnFind = true;
            }
        }
        
        if(!$blnFind || $blnSameNameAndId) {
            // Checking the slug attribute
            try{
                $Elements = $Repository->findBySlug($Object->getSlug());
            } catch (\Exception $ex) {
                $this->getLogger()
                        ->error('Error during the research of a ' . 
                            $strObjectClassName. " " . $ex->getMessage());
                throw $this->createNotFoundException(
                    $this->getTranslator()->trans('SearchError')
                );
            }

            if(count($Elements) > 1) {
                $this->getLogger()
                        ->error('More than one occurence found for ' . 
                            $strObjectClassName. " " . $ex->getMessage());
                throw $this->createNotFoundException(
                    $this->getTranslator()->trans('MultipleFound',
                        array('%field%' => $this->getTranslator()->trans("Slug"))
                    )
                );
            } elseif(count($Elements) == 1) {
                $Result = $Elements[0];
                if($Result->getId() == $Object->getId()) {
                    $blnSameSlugAndId = true;
                } else {
                    $strReturn = "Slug";
                    $blnFind = true;
                }
            }
        }
        
        // Check if it is the same object
        if($blnSameNameAndId && $blnSameSlugAndId) {
            $strReturn = "Same";
        } elseif($blnFind) {
            //...
        } else {
            $strReturn = "NoExists";
        }
        
        
        return $strReturn;
    }
    
    /**
     * 
     * Transform a string to another one matching the slug pattern
     * 
     * @param string $strProposedSlug is the initial proposed slug
     * @return string is the final slug (possibly empty!)
     * 
     * @author StackOverFlow
     */
    public function slugify($strProposedSlug) {
        
        if(!preg_match("/^[a-z0-9-]+$/", $strProposedSlug)) {
            
            // replace non letter or digits by -
            $strProposedSlug = preg_replace('~[^\pL\d]+~u', '-', $strProposedSlug);

            // transliterate
            $strProposedSlug = iconv('utf-8', 'us-ascii//TRANSLIT', $strProposedSlug);

            // remove unwanted characters
            $strProposedSlug = preg_replace('~[^-\w]+~', '', $strProposedSlug);

            // trim
            $strProposedSlug = trim($strProposedSlug, '-');

            // remove duplicate -
            $strProposedSlug = preg_replace('~-+~', '-', $strProposedSlug);

            // lowercase
            $strProposedSlug = strtolower($strProposedSlug);

        }
        
        return $strProposedSlug;
    }
    
    /**
     * 
     * @param int $intQtyPerPage is the number of elements per page
     * @param int $intPage is the start page
     * @param array $ArrayOfElements contains all the elements
     * @return array of the elements of the current page
     * @author Eric COURTIAL
     */
    public function getElementsOfPage($intQtyPerPage, $intPage, array $ArrayOfElements) {
        
        $arrayResults = array();
        
        if($intPage == 1) {
            $intStart = 0;
        } else {
            $intStart = ($intPage - 1) * $intQtyPerPage;
        }
            
        if(count($ArrayOfElements) >= $intStart + 1) {
            for($intCount = 0; $intCount < $intStart; $intCount++) {
                next($ArrayOfElements);
            }

            for($intCount = 0; 
                current($ArrayOfElements) !== false 
                    && $intCount < $intQtyPerPage ; $intCount++) {
                $arrayResults[] = current($ArrayOfElements);
                next($ArrayOfElements);
            }
        }
            
        return $arrayResults;
    }
    
    /**
     * Check if books of the same support (paper, numeric...) have the 
     * same ISBN number
     * @param string $strISBN
     * @param int $intType if of the type of the support
     * @param int $bookId (optionnal): book to exclude from the comparison
     * @return boolean
     */
    public function checkISBN($strISBN, $intType, $bookId = 0) {
        
        $repo = $this->container->get('doctrine')
            ->getRepository('AppBundle:Book');
        
        $bnlExists = false;
        $books = $repo->findByIsbn($strISBN);

        foreach($books as $book) {
            if($book->getMaterial()->getId() == $intType) {
                $bnlExists = true;
            }
            
            if($bnlExists && $bookId != 0 && $book->getId() == $bookId) { // Same books, doesn't matter
                $bnlExists = false;
            }
            
            if($bnlExists) {
                break;
            }
        }
        
        return $bnlExists;
    }
}