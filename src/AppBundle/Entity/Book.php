<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Book
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\BookRepository")
 * @ORM\Table(name="myx_book", uniqueConstraints={@ORM\UniqueConstraint(name="slug", columns={"slug"})}, indexes={@ORM\Index(name="title", columns={"title"}), @ORM\Index(name="slug", columns={"slug"}), @ORM\Index(name="description", columns={"description"}), @ORM\Index(name="language", columns={"language"}), @ORM\Index(name="format", columns={"format"}), @ORM\Index(name="editor", columns={"editor"}), @ORM\Index(name="user_id", columns={"user_id"})})
 */
class Book
{
    // How many books will be displayed on a results page
    const QTY_BOOKS = 5;
    
    // the authorized types of research and the corresponding field
    public static $arrSearchTypes = array(
      'Title' => 'title',
      'Keywords' => 'keywords',
      'Description' => 'description'
    );
    
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=100, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="integer", nullable=false)
     */
    private $year;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="addition_date", type="date", nullable=false)
     */
    private $additionDate;

    /**
     * @var string
     *
     * @ORM\Column(name="isbn", type="string", length=13, nullable=true)
     */
    private $isbn;
    
    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="string", length=100, nullable=true)
     */
    private $keywords;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Editor
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Editor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="editor", referencedColumnName="id")
     * })
     */
    private $editor;

    /**
     * @var \AppBundle\Entity\Language
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Language")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="language", referencedColumnName="id")
     * })
     */
    private $language;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \AppBundle\Entity\Format
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Format")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="format", referencedColumnName="id")
     * })
     */
    private $format;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Location", mappedBy="book")
     */
    private $location;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Kind", mappedBy="book")
     */
    private $kind;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Author", mappedBy="book")
     */
    private $author;
    
    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=100, nullable=false)
     */
    private $slug;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_modified", type="date", nullable=true)
     */
    private $lastModified;
    
    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_user_id", referencedColumnName="id")
     * })
     */
    private $lastUser;
    
    /**
     * @var \AppBundle\Entity\Material
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Material")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="material", referencedColumnName="id")
     * })
     */
    private $material;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->location = new \Doctrine\Common\Collections\ArrayCollection();
        $this->kind = new \Doctrine\Common\Collections\ArrayCollection();
        $this->author = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * toString
     * @return string
     */
    public function __toString() {
        return $this->getTitle();
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Book
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Book
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
     * Set description
     *
     * @param string $description
     *
     * @return Book
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set year
     *
     * @param integer $year
     *
     * @return Book
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set additionDate
     *
     * @param \DateTime $additionDate
     *
     * @return Book
     */
    public function setAdditionDate($additionDate)
    {
        $this->additionDate = $additionDate;

        return $this;
    }

    /**
     * Get additionDate
     *
     * @return \DateTime
     */
    public function getAdditionDate()
    {
        return $this->additionDate;
    }

    /**
     * Set isbn
     *
     * @param string $isbn
     *
     * @return Book
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * Get isbn
     *
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }
    
    /**
     * Get isbn or message
     *
     * @return string
     */
    public function getIsbnContent()
    {
        $strReturn = "";
        if(mb_strlen($this->getIsbn()) == 0) {
            $strReturn = "NotGiven";
        } else {
            $strReturn = $this->getIsbn();
        }
        
        return $strReturn;
    }
    
    /**
     * Set keywords
     *
     * @param string $keywords
     *
     * @return Book
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeyWords()
    {
        return $this->keywords;
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
     * Set editor
     *
     * @param \AppBundle\Entity\BEditor $editor
     *
     * @return Book
     */
    public function setEditor(\AppBundle\Entity\Editor $editor = null)
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * Get editor
     *
     * @return \AppBundle\Entity\BEditor
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * Set language
     *
     * @param \AppBundle\Entity\Language $language
     *
     * @return Book
     */
    public function setLanguage(\AppBundle\Entity\Language $language = null)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return \AppBundle\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Book
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set format
     *
     * @param \AppBundle\Entity\Format $format
     *
     * @return Book
     */
    public function setFormat(\AppBundle\Entity\Format $format = null)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return \AppBundle\Entity\Format
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Add location
     *
     * @param \AppBundle\Entity\BLocation $location
     *
     * @return Book
     */
    public function addLocation(\AppBundle\Entity\Location $location)
    {
        $this->location[] = $location;

        return $this;
    }

    /**
     * Remove location
     *
     * @param \AppBundle\Entity\Location $location
     */
    public function removeLocation(\AppBundle\Entity\Location $location)
    {
        $this->location->removeElement($location);
    }

    /**
     * Get location
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Add kind
     *
     * @param \AppBundle\Entity\Kind $kind
     *
     * @return Book
     */
    public function addKind(\AppBundle\Entity\Kind $kind)
    {
        $this->kind[] = $kind;

        return $this;
    }

    /**
     * Remove kind
     *
     * @param \AppBundle\Entity\Kind $kind
     */
    public function removeKind(\AppBundle\Entity\Kind $kind)
    {
        $this->kind->removeElement($kind);
    }

    /**
     * Get Kind
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * Add author
     *
     * @param \AppBundle\Entity\Author $author
     *
     * @return Book
     */
    public function addAuthor(\AppBundle\Entity\Author $author)
    {
        $this->author[] = $author;

        return $this;
    }

    /**
     * Remove author
     *
     * @param \AppBundle\Entity\Author $author
     */
    public function removeAuthor(\AppBundle\Entity\Author $author)
    {
        $this->author->removeElement($author);
    }

    /**
     * Get author
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthor()
    {
        return $this->author;
    }
    
    
    /**
     * Get date of last edition
     *
     * @return \DateTime
     */
    function getLastModified() {
        return $this->lastModified;
    }

    /**
     * Get last user to edit this book
     *
     * @return \AppBundle\Entity\User
     */
    function getLastUser() {
        return $this->lastUser;
    }

    /**
     * Set date of last edition
     *
     * @param \DateTime $lastModified
     *
     * @return Book
     */
    function setLastModified(\DateTime $lastModified) {
        $this->lastModified = $lastModified;
    }

    /**
     * Set last user to edit this book
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Book
     */
    function setLastUser(\AppBundle\Entity\User $user) {
        $this->lastUser = $user;
    }
    
    /**
     * Set editor
     *
     * @param \AppBundle\Entity\Material $material
     *
     * @return Book
     */
    public function setMaterial(\AppBundle\Entity\Material $material = null)
    {
        $this->material = $material;

        return $this;
    }

    /**
     * Get editor
     *
     * @return \AppBundle\Entity\BEditor
     */
    public function getMaterial()
    {
        return $this->material;
    }
}
