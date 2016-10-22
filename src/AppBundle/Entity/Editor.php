<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Editor
 *
 * @ORM\Table(name="myx_editor", uniqueConstraints={@ORM\UniqueConstraint(name="Nom", columns={"name"})}, uniqueConstraints={@ORM\UniqueConstraint(name="slug", columns={"slug"})}, indexes={@ORM\Index(name="name", columns={"name"}), @ORM\Index(name="slug", columns={"slug"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\EditorRepository")
 */
class Editor
{
    
    // How many editors will be displayed on a results page or on the index
    // page of the editors
    const QTY_EDITORS = 5;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "name.too.short",
     *      maxMessage = "name.too.long"
     * )
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * Set name
     *
     * @param string $name
     *
     * @return Editor
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
     * Set slug
     *
     * @param string $slug
     *
     * @return Editor
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
