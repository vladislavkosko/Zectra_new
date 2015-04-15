<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Thread
 *
 * @ORM\Table(name="header_forum_threads")
 * @ORM\Entity
 */
class Thread
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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userID;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ThreadPost", mappedBy="thread", cascade={"remove"})
     */
    private $posts;

    /**
     * @var boolean
     * @ORM\Column(name="sticky", type="boolean", options={"default" = false})
     */
    private $sticky;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var int
     * @ORM\Column(name="subheader_id", type="integer")
     */
    private $subHeaderID;

    /**
     * @var SubHeader
     * @ORM\ManyToOne(targetEntity="SubHeader", inversedBy="threads")
     * @ORM\JoinColumn(name="subheader_id", referencedColumnName="id")
     */
    private $subHeader;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="message", type="string", length=5000)
     */
    private $message;

    /**
     * @var string
     * @ORM\Column(name="keywords", type="string", length=255, nullable=true, options={"default" = null})
     */
    private $keywords;

    public function __construct() {
        $this->posts = new ArrayCollection();
        $this->date = new \DateTime();
        $this->sticky = false;
    }

    /**
     * @return array
     */
    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'userID' => $this->getUserID(),
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'subHeaderID' => $this->getSubHeaderID(),
            'keywords' => $this->getKeywords(),
            'posts' => EntityOperations::arrayToJsonArray($this->getPosts()),
        );
    }

    /**
     * @param EntityManager $em
     * @param int $subheader_id
     * @param int $user_id
     * @param array $params
     * @return Thread
     */
    public static function startNewThread(EntityManager $em, $subheader_id, $user_id, $params) {
        $subheader = $em->getRepository('ZectranetBundle:SubHeader')->find($subheader_id);
        $user = $em->getRepository('ZectranetBundle:User')->find($user_id);
        $thread = new Thread();
        $thread->setMessage($params['message']);
        $thread->setTitle($params['title']);
        $thread->setKeywords($params['keywords']);
        $thread->setSubHeader($subheader);
        $thread->setUser($user);
        $em->persist($thread);
        $em->flush();

        return $thread;
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
     * Set userID
     *
     * @param integer $userID
     * @return Thread
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;

        return $this;
    }

    /**
     * Get userID
     *
     * @return integer 
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * Set subHeaderID
     *
     * @param integer $subHeaderID
     * @return Thread
     */
    public function setSubHeaderID($subHeaderID)
    {
        $this->subHeaderID = $subHeaderID;

        return $this;
    }

    /**
     * Get subHeaderID
     *
     * @return integer 
     */
    public function getSubHeaderID()
    {
        return $this->subHeaderID;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Thread
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
     * Set message
     *
     * @param string $message
     * @return Thread
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set user
     *
     * @param \ZectranetBundle\Entity\User $user
     * @return Thread
     */
    public function setUser(\ZectranetBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \ZectranetBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set subHeader
     *
     * @param \ZectranetBundle\Entity\SubHeader $subHeader
     * @return Thread
     */
    public function setSubHeader(\ZectranetBundle\Entity\SubHeader $subHeader = null)
    {
        $this->subHeader = $subHeader;

        return $this;
    }

    /**
     * Get subHeader
     *
     * @return \ZectranetBundle\Entity\SubHeader 
     */
    public function getSubHeader()
    {
        return $this->subHeader;
    }

    /**
     * Add posts
     *
     * @param \ZectranetBundle\Entity\ThreadPost $posts
     * @return Thread
     */
    public function addPost(\ZectranetBundle\Entity\ThreadPost $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param \ZectranetBundle\Entity\ThreadPost $posts
     */
    public function removePost(\ZectranetBundle\Entity\ThreadPost $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Set sticky
     *
     * @param boolean $sticky
     * @return Thread
     */
    public function setSticky($sticky)
    {
        $this->sticky = $sticky;

        return $this;
    }

    /**
     * Get sticky
     *
     * @return boolean 
     */
    public function getSticky()
    {
        return $this->sticky;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Thread
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Thread
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
    public function getKeywords()
    {
        return $this->keywords;
    }
}
