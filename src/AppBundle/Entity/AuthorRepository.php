<?php

namespace AppBundle\Entity;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * AuthorRepository
 *
 * This is the repository for the "Author" entities.
 * 
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 * 
 * @author Eric COURTIAL
 * 
 */
class AuthorRepository extends \Doctrine\ORM\EntityRepository {
    
    /**
     * 
     * Return the most productives authors.
     * 
     * @param int $intQty : the max number of results returned.
     * @return mixed array of array, each one containing id and name 
     * of the author. 
     * @throws \ErrorException
     * @throws NoResultException
     * 
     * @author Eric COURTIAL
     *
     */
    public function findByTopAuthors($intQty) {
        
         $strQuery = $this->getEntityManager()->createQuery(
                'SELECT COUNT( a.id ) AS qty_occurences, 
                a.id,
                a.name,
                a.surname
                FROM AppBundle:Author a
                JOIN a.book b
                GROUP BY a.id
                ORDER BY qty_occurences DESC'
            )
            ->setMaxResults($intQty);
        
        try {
            $arrQueryResults = $strQuery->getResult();
        } catch (Exception $exception) {
            throw $exception;
        }

        // The SQL request must return at least one result
        if(count($arrQueryResults) == 0) {
            throw new NoResultException;
        }

        return $arrQueryResults;
        
    }
    
    /**
     * 
     * Return all the authors sorted by surname.
     * 
     * @param int $intPage is the number of the page of results
     * @return mixed array of author entity. 
     * @throws \ErrorException
     * @throws NoResultException
     * 
     * @author Eric COURTIAL
     *
     */
    public function findAllOrderedBySurname($intPage = 1) {
        
        $intRefValue = Author::QTY_AUTHORS;
        
        if($intPage > 1) {
            $intStart = (($intPage - 1) * $intRefValue);
            $intEnd = $intStart + $intRefValue - 1;
        } else {
            $intStart = 0;
            $intEnd = $intRefValue - 1;
        }
        
        $strQuery = $this->createQueryBuilder('p')
            ->setFirstResult($intStart)
            ->setMaxResults($intRefValue)
            ->orderBy('p.surname', 'ASC');
        
        try {
            $arrQueryResults = new Paginator($strQuery);
        } catch (Exception $exception) {
            throw $exception;
        }

        // The SQL request must return at least one result
        if(count($arrQueryResults) == 0) {
            throw new NoResultException;
        }

        return $arrQueryResults;
        
    }
    
    /**
     * 
     * Return all the authors sorted by surname, but WITHOUT pagination.
     * 
     * @param int $intPage is the number of the page of results
     * @return mixed array of Autho entity. 
     * @throws \ErrorException
     * @throws NoResultException
     * 
     * @author Eric COURTIAL
     *
     */
    public function findAllOrderedBySurNameWp() {
        
        $strQuery = $this->createQueryBuilder('p')
            ->orderBy('p.surname', 'ASC')
            ->getQuery();
        
        try {
            $arrQueryResults = $strQuery->getResult();
        } catch (Exception $exception) {
            throw $exception;
        }

        // The SQL request must return at least one result
        if(count($arrQueryResults) == 0) {
            throw new NoResultException;
        }

        return $arrQueryResults;
        
    }
    
    /**
     * 
     * search for authors by surname.
     *
     * @param string $strName is the occurence to look for
     * @param int $intPage is the number of the page of results
     * @return mixed array of Author entity. 
     * @throws \ErrorException
     * @throws NoResultException
     * 
     * @author Eric COURTIAL
     *
     */
    public function searchBySurname($strName, $intPage = 1) {

        $intRefValue = Author::QTY_AUTHORS;
        
        if($intPage > 1) {
            $intStart = (($intPage - 1) * $intRefValue);
            $intEnd = $intStart + $intRefValue - 1;
        } else {
            $intStart = 0;
            $intEnd = $intRefValue - 1;
        }
        
        $strQuery = $this->createQueryBuilder('p')
            ->Where('p.surname LIKE :Request')
            ->setFirstResult($intStart)
            ->setMaxResults($intRefValue)
            ->setParameter('Request', "%$strName%");
        
        try {
            $arrQueryResults = new Paginator($strQuery);
        } catch (Exception $exception) {
            throw $exception;
        }

        // The SQL request must return at least one result
        if(count($arrQueryResults) == 0) {
            throw new NoResultException;
        }

        return $arrQueryResults;
        
    }
    
    /**
     * Remove all the elements regarding one specific book
     * in the join table 
     * 
     * @param type $intBookId is the id of the book
     * @throws \AppBundle\Entity\Exception
     * 
     * @author Eric COURTIAL
     */
    public function removeByBookId($intBookId) {

        try {
            $query = $this->getEntityManager()->getConnection()->prepare('DELETE FROM myx_publications WHERE book_id = :id');
            $query->execute(array('id' => $intBookId));
        } catch (Exception $exception) {
            throw $exception;
        }
        
    }
    
}
