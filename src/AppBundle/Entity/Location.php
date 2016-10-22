<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Location
 *
 * A location is a place where you can store a book. A book can be stored 
 * at only one location. But if you have many copies of this book, there is 
 * obviously many locations.
 *
 * @ORM\Table(name="myx_location", uniqueConstraints={@ORM\UniqueConstraint(name="Nom", columns={"name"})},  uniqueConstraints={@ORM\UniqueConstraint(name="slug", columns={"slug"})}, indexes={@ORM\Index(name="name", columns={"name"}), @ORM\Index(name="slug", columns={"slug"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\LocationRepository")
 */
class Location {
    
    // How many locations will be displayed on a results page
    const QTY_LOCATIONS = 5;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "humanName.too.short",
     *      maxMessage = "humanName.too.long"
     * )
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=100, nullable=false)
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "slug.too.short",
     *      maxMessage = "slug.too.long"
     * )
     */
    private $slug;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Book", inversedBy="location")
     * @ORM\JoinTable(name="myx_stored_in",
     *   joinColumns={
     *     @ORM\JoinColumn(name="Location_Id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     *   }
     * )
     */
    private $book;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->book = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return Location
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add book
     *
     * @param \AppBundle\Entity\Book $book
     *
     * @return Location
     */
    public function addBook(\AppBundle\Entity\Book $book)
    {
        $this->book[] = $book;

        return $this;
    }

    /**
     * Remove book
     *
     * @param \AppBundle\Entity\Book $book
     */
    public function removeBook(\AppBundle\Entity\Book $book)
    {
        $this->book->removeElement($book);
    }

    /**
     * Get book
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBook()
    {
        return $this->book;
    }
    
    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Location
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
    
    /**
     * toString
     * @return string
     */
    public function __toString() {
        return $this->getName();
    }
}
