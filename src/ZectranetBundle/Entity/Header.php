<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SubHeader
 *
 * @ORM\Table(name="header_forum_headers")
 * @ORM\Entity
 */
class Header
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
     * @ORM\Column(name="forum_id", type="integer")
     */
    private $forumID;

    /**
     * @var HeaderForum
     * @ORM\ManyToOne(targetEntity="HeaderForum", inversedBy="headers")
     * @ORM\JoinColumn(name="forum_id", referencedColumnName="id")
     */
    private $forum;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="SubHeader", mappedBy="header", cascade={"remove"})
     */
    private $subHeaders;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="header_background_color", type="string", length=20)
     */
    private $headerBgColor;

    /**
     * @var string
     * @ORM\Column(name="header_text_color", type="string", length=20)
     */
    private $headerTextColor;

    /**
     * @return array
     */
    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'forumID' => $this->getForumID(),
            'headerBgColor' => $this->getHeaderBgColor(),
            'headerTextColor' => $this->getHeaderTextColor(),
            'subHeaders' => EntityOperations::arrayToJsonArray($this->getSubHeaders()),
            'title' => $this->getTitle(),
        );
    }

    /**
     * @param EntityManager $em
     * @param int $project_id
     * @param array $params
     * @return Header
     */
    public static function addNewHeader(EntityManager $em, $project_id, $params) {
        $project = $em->getRepository('ZectranetBundle:HeaderForum')->find($project_id);
        $header = new Header();
        $header->setTitle($params['title']);
        $header->setHeaderBgColor($params['bgColor']);
        $header->setHeaderTextColor($params['textColor']);
        $header->setForum($project);
        $em->persist($header);
        $em->flush();

        return $header;
    }

    /**
     * @param EntityManager $em
     * @param array $params
     * @return Header
     */
    public static function addNewSubHeader(EntityManager $em, $params) {
        /** @var Header $header */
        $header = $em->getRepository('ZectranetBundle:Header')->find($params['header_id']);
        $subHeader = new SubHeader();
        $subHeader->setTitle($params['title']);
        $subHeader->setAdminHeader($params['admin']);
        $subHeader->setDescription($params['description']);
        $subHeader->setHeader($header);
        $em->persist($subHeader);
        $em->flush();

        return $subHeader;
    }

    /**
     * @param EntityManager $em
     * @param int $header_id
     */
    public static function deleteHeader(EntityManager $em, $header_id) {
        $forum = $em->getRepository('ZectranetBundle:HeaderForum')->find($header_id);
        $em->remove($forum);
        $em->flush();
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
     * Set forumID
     *
     * @param integer $forumID
     * @return Header
     */
    public function setForumID($forumID)
    {
        $this->forumID = $forumID;

        return $this;
    }

    /**
     * Get forumID
     *
     * @return integer 
     */
    public function getForumID()
    {
        return $this->forumID;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Header
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
     * Set headerBgColor
     *
     * @param string $headerBgColor
     * @return Header
     */
    public function setHeaderBgColor($headerBgColor)
    {
        $this->headerBgColor = $headerBgColor;

        return $this;
    }

    /**
     * Get headerBgColor
     *
     * @return string 
     */
    public function getHeaderBgColor()
    {
        return $this->headerBgColor;
    }

    /**
     * Set headerTextColor
     *
     * @param string $headerTextColor
     * @return Header
     */
    public function setHeaderTextColor($headerTextColor)
    {
        $this->headerTextColor = $headerTextColor;

        return $this;
    }

    /**
     * Get headerTextColor
     *
     * @return string 
     */
    public function getHeaderTextColor()
    {
        return $this->headerTextColor;
    }

    /**
     * Set forum
     *
     * @param \ZectranetBundle\Entity\HeaderForum $forum
     * @return Header
     */
    public function setForum(\ZectranetBundle\Entity\HeaderForum $forum = null)
    {
        $this->forum = $forum;

        return $this;
    }

    /**
     * Get forum
     *
     * @return \ZectranetBundle\Entity\HeaderForum 
     */
    public function getForum()
    {
        return $this->forum;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->subHeaders = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add subHeaders
     *
     * @param \ZectranetBundle\Entity\SubHeader $subHeaders
     * @return Header
     */
    public function addSubHeader(\ZectranetBundle\Entity\SubHeader $subHeaders)
    {
        $this->subHeaders[] = $subHeaders;

        return $this;
    }

    /**
     * Remove subHeaders
     *
     * @param \ZectranetBundle\Entity\SubHeader $subHeaders
     */
    public function removeSubHeader(\ZectranetBundle\Entity\SubHeader $subHeaders)
    {
        $this->subHeaders->removeElement($subHeaders);
    }

    /**
     * Get subHeaders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubHeaders()
    {
        return $this->subHeaders;
    }
}
