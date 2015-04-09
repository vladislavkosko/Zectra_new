<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SubHeader
 *
 * @ORM\Table(name="header_forum_subheaders")
 * @ORM\Entity
 */
class SubHeader
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="header_id", type="integer")
     */
    private $headerID;

    /**
     * @var Header
     * @ORM\ManyToOne(targetEntity="Header", inversedBy="subHeaders")
     * @ORM\JoinColumn(name="header_id", referencedColumnName="id")
     */
    private $header;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Thread", mappedBy="subHeader", cascade={"remove"})
     * @ORM\OrderBy({"sticky" = "DESC", "date" = "DESC"})
     */
    private $threads;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=150)
     */
    private $title;

    /**
     * @var boolean
     * @ORM\Column(name="admin_header", type="boolean")
     */
    private $adminHeader;

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=500)
     */
    private $description;

    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'isAdminHeader' => $this->getAdminHeader(),
            'description' => $this->getDescription(),
            'headerID' => $this->getHeaderID(),
            'threads' => EntityOperations::arrayToJsonArray($this->getThreads()),
        );
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
     * Set headerID
     *
     * @param integer $headerID
     * @return SubHeader
     */
    public function setHeaderID($headerID)
    {
        $this->headerID = $headerID;

        return $this;
    }

    /**
     * Get headerID
     *
     * @return integer 
     */
    public function getHeaderID()
    {
        return $this->headerID;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return SubHeader
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
     * Set adminHeader
     *
     * @param boolean $adminHeader
     * @return SubHeader
     */
    public function setAdminHeader($adminHeader)
    {
        $this->adminHeader = $adminHeader;

        return $this;
    }

    /**
     * Get adminHeader
     *
     * @return boolean 
     */
    public function getAdminHeader()
    {
        return $this->adminHeader;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return SubHeader
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
     * Set header
     *
     * @param \ZectranetBundle\Entity\Header $header
     * @return SubHeader
     */
    public function setHeader(\ZectranetBundle\Entity\Header $header = null)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Get header
     *
     * @return \ZectranetBundle\Entity\Header 
     */
    public function getHeader()
    {
        return $this->header;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->threads = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add threads
     *
     * @param \ZectranetBundle\Entity\Thread $threads
     * @return SubHeader
     */
    public function addThread(\ZectranetBundle\Entity\Thread $threads)
    {
        $this->threads[] = $threads;

        return $this;
    }

    /**
     * Remove threads
     *
     * @param \ZectranetBundle\Entity\Thread $threads
     */
    public function removeThread(\ZectranetBundle\Entity\Thread $threads)
    {
        $this->threads->removeElement($threads);
    }

    /**
     * Get threads
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getThreads()
    {
        return $this->threads;
    }
}
