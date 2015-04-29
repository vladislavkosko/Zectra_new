<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * HFSubHeader
 *
 * @ORM\Table(name="header_forum_headers")
 * @ORM\Entity
 */
class HFHeader
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
     * @var HFForum
     * @ORM\ManyToOne(targetEntity="HFForum", inversedBy="headers")
     * @ORM\JoinColumn(name="forum_id", referencedColumnName="id")
     */
    private $forum;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="HFSubHeader", mappedBy="header", cascade={"remove"})
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
     * @return HFHeader
     */
    public static function addNewHeader(EntityManager $em, $project_id, $params) {
        $project = $em->getRepository('ZectranetBundle:HFForum')->find($project_id);
        $header = new HFHeader();
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
     * @return HFHeader
     */
    public static function addNewSubHeader(EntityManager $em, $params) {
        /** @var HFHeader $header */
        $header = $em->getRepository('ZectranetBundle:HFHeader')->find($params['header_id']);
        $subHeader = new HFSubHeader();
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
        $forum = $em->getRepository('ZectranetBundle:HFForum')->find($header_id);
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
     * @return HFHeader
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
     * @return HFHeader
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
     * @return HFHeader
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
     * @return HFHeader
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
     * @param \ZectranetBundle\Entity\HFForum $forum
     * @return HFHeader
     */
    public function setForum(\ZectranetBundle\Entity\HFForum $forum = null)
    {
        $this->forum = $forum;

        return $this;
    }

    /**
     * Get forum
     *
     * @return \ZectranetBundle\Entity\HFForum
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
     * @param \ZectranetBundle\Entity\HFSubHeader $subHeaders
     * @return HFHeader
     */
    public function addSubHeader(\ZectranetBundle\Entity\HFSubHeader $subHeaders)
    {
        $this->subHeaders[] = $subHeaders;

        return $this;
    }

    /**
     * Remove subHeaders
     *
     * @param \ZectranetBundle\Entity\HFSubHeader $subHeaders
     */
    public function removeSubHeader(\ZectranetBundle\Entity\HFSubHeader $subHeaders)
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
