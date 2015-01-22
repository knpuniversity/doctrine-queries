<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CategoryRepository")
 */
class Category
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="iconKey", type="string", length=20)
     */
    private $iconKey;

    /**
     * @ORM\OneToMany(targetEntity="FortuneCookie", mappedBy="category")
     */
    private $fortuneCookies;

    public function __construct()
    {
        $this->fortuneCookies = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Category
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
     * Set iconKey
     *
     * @param string $iconKey
     * @return Category
     */
    public function setIconKey($iconKey)
    {
        $this->iconKey = $iconKey;

        return $this;
    }

    /**
     * Get iconKey
     *
     * @return string 
     */
    public function getIconKey()
    {
        return $this->iconKey;
    }

    /**
     * @return ArrayCollection|FortuneCookie[]
     */
    public function getFortuneCookies()
    {
        return $this->fortuneCookies;
    }
}
