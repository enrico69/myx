<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="myx_user", uniqueConstraints={@ORM\UniqueConstraint(name="Email", columns={"Email"})}, uniqueConstraints={@ORM\UniqueConstraint(name="slug", columns={"slug"})}, indexes={@ORM\Index(name="slug", columns={"slug"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     * @Assert\NotBlank(groups={"Registration"}) 
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "user.name.too.short",
     *      maxMessage = "user.name.too.long"
     * )
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=50, nullable=false)
     * @Assert\NotBlank(groups={"Registration"}) 
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "user.surname.too.short",
     *      maxMessage = "user.surname.too.long"
     * )
     */
    protected $surname;
    
    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=2, nullable=false)
     * @Assert\NotBlank(groups={"Registration"}) 
     */
    protected $locale;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
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
     * Set surname
     *
     * @param string $surname
     *
     * @return User
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
     * toString
     * @return string
     */
    public function __toString() {
        return $this->getName() . " " . $this->getSurname();
    }
    
    function getLocale() {
        return $this->locale;
    }

    function setLocale($locale) {
        $this->locale = $locale;
    }

}
