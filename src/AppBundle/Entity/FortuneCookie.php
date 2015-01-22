<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FortuneCookie
 *
 * @ORM\Table(name="fortune_cookie")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\FortuneCookieRepository")
 */
class FortuneCookie
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
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="fortuneCookies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="fortune", type="string", length=255)
     */
    private $fortune;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="numberPrinted", type="integer")
     */
    private $numberPrinted;

    /**
     * @var bool Is this FortuneCookie discontinued?
     *
     * @ORM\Column(name="discontinued", type="boolean")
     */
    private $discontinued = false;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * Set fortune
     *
     * @param string $fortune
     * @return FortuneCookie
     */
    public function setFortune($fortune)
    {
        $this->fortune = $fortune;

        return $this;
    }

    /**
     * Get fortune
     *
     * @return string 
     */
    public function getFortune()
    {
        return $this->fortune;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return FortuneCookie
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set numberPrinted
     *
     * @param integer $numberPrinted
     * @return FortuneCookie
     */
    public function setNumberPrinted($numberPrinted)
    {
        $this->numberPrinted = $numberPrinted;

        return $this;
    }

    /**
     * Get numberPrinted
     *
     * @return integer 
     */
    public function getNumberPrinted()
    {
        return $this->numberPrinted;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return boolean
     */
    public function isDiscontinued()
    {
        return $this->discontinued;
    }

    /**
     * @param boolean $discontinued
     */
    public function setDiscontinued($discontinued)
    {
        $this->discontinued = $discontinued;
    }
}
