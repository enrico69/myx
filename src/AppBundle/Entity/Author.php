<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Author
 *
 * @ORM\Table(name="myx_author", indexes={@ORM\Index(name="name", columns={"name"}), @ORM\Index(name="slug", columns={"slug"})}, uniqueConstraints={@ORM\UniqueConstraint(name="slug", columns={"slug"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\AuthorRepository")
 */
class Author
{
    
    // How many authors will be displayed on a results page
    const QTY_AUTHORS = 5;
    
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
     * @ORM\Column(name="surname", type="string", length=50, nullable=false)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "surname.too.short",
     *      maxMessage = "surname.too.long"
     * )
     */
    private $surname;
    
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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Book", inversedBy="author")
     * @ORM\JoinTable(name="myx_publications",
     *   joinColumns={
     *     @ORM\JoinColumn(name="Author_Id", referencedColumnName="id")
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
     * @return Author
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
     * Set slug
     *
     * @param string $slug
     *
     * @return Author
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
     * Set surname
     *
     * @param string $surname
     *
     * @return Author
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return strtoupper($this->surname);
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
     * @return BAuthor
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
     * toString
     * @return string
     */
    public function __toString() {
        return $this->getSurname() . " " . $this->getName();
    }
}
