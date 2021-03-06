<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * LocationRepository
 *
 * This is the repository for the "Location" entities.
 * 
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 * 
 * @author Eric COURTIAL
 */
class LocationRepository extends EntityRepository {
    
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
            $query = $this->getEntityManager()->getConnection()->prepare('DELETE FROM myx_stored_in WHERE book_id = :id');
            $query->execute(array('id' => $intBookId));
        } catch (Exception $exception) {
            throw $exception;
        }
        
    }
    
    /**
     * 
     * Return the locations the most used to store media.
     * 
     * @param int $intQty : the max number of results returned.
     * @return mixed array of array, each one containing id and name 
     * of the location. 
     * @throws \ErrorException
     * @throws NoResultException
     * 
     * @author Eric COURTIAL
     *
     */
    public function findByTopLocations($intQty) {
        
        $strQuery = $this->getEntityManager()->createQuery(
                'SELECT COUNT( l.id ) AS qty_occurences, 
                l.id,
                l.name
                FROM AppBundle:Location l
                JOIN l.book b
                GROUP BY l.id
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
     * Return all the locations sorted by surname.
     * 
     * @param int $intPage is the number of the page of results
     * @return mixed array of kind entity. 
     * @throws \ErrorException  
     * @throws NoResultException
     * 
     * @author Eric COURTIAL
     *
     */
    public function findAllOrderedByName($intPage = 1) {
        
        $intRefValue = Location::QTY_LOCATIONS;
        
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
            ->orderBy('p.name', 'ASC');
        
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
     * Return all the locations sorted by name, but WITHOUT pagination.
     * 
     * @param int $intPage is the number of the page of results
     * @return mixed array of Location entity. 
     * @throws \ErrorException
     * @throws NoResultException
     * 
     * @author Eric COURTIAL
     *
     */
    public function findAllOrderedByNameWp() {
        
        $strQuery = $this->createQueryBuilder('p')
            ->orderBy('p.name', 'ASC')
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
     * search for locations by surname.
     *
     * @param string $strName is the occurence to look for
     * @param int $intPage is the number of the page of results
     * @return mixed array of Kind entity. 
     * @throws \ErrorException
     * @throws NoResultException
     * 
     * @author Eric COURTIAL
     *
     */
    public function searchByName($strName, $intPage = 1) {

        $intRefValue = Location::QTY_LOCATIONS;
        
        if($intPage > 1) {
            $intStart = (($intPage - 1) * $intRefValue);
            $intEnd = $intStart + $intRefValue - 1;
        } else {
            $intStart = 0;
            $intEnd = $intRefValue - 1;
        }
        
        $strQuery = $this->createQueryBuilder('p')
            ->Where('p.name LIKE :Request')
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
    
}