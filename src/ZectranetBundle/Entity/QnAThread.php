<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * QnAThread
 *
 * @ORM\Table(name="QnA_threads")
 * @ORM\Entity
 */
class QnAThread
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
     * @ORM\OneToMany(targetEntity="QnAPost", mappedBy="thread", cascade={"remove"})
     */
    private $posts;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

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
     * @ORM\Column(name="keywords", type="string", length=255)
     */
    private $keywords;

    /**
     * @var int
     * @ORM\Column(name="forum_id", type="integer")
     */
    private $forumID;

    /**
     * @var QnAForum
     * @ORM\ManyToOne(targetEntity="QnAForum", inversedBy="threads")
     * @ORM\JoinColumn(name="forum_id", referencedColumnName="id")
     */
    private $forum;

    public function getInArray() {
        return array(
           'id' => $this->getId(),
           'title' => $this->getTitle(),
           'message' => $this->getMessage(),
           'forumID' => $this->getForumID(),
        );
    }

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->date = new \DateTime();
        $this->keywords = '';
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
     * @return QnAThread
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
     * Set date
     *
     * @param \DateTime $date
     * @return QnAThread
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
     * Set title
     *
     * @param string $title
     * @return QnAThread
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
     * @return QnAThread
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
     * Set keywords
     *
     * @param string $keywords
     * @return QnAThread
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

    /**
     * Set user
     *
     * @param \ZectranetBundle\Entity\User $user
     * @return QnAThread
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
     * Add posts
     *
     * @param \ZectranetBundle\Entity\QnAPost $posts
     * @return QnAThread
     */
    public function addPost(\ZectranetBundle\Entity\QnAPost $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param \ZectranetBundle\Entity\QnAPost $posts
     */
    public function removePost(\ZectranetBundle\Entity\QnAPost $posts)
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
     * Set forumID
     *
     * @param integer $forumID
     * @return QnAThread
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
     * Set forum
     *
     * @param \ZectranetBundle\Entity\QnAForum $forum
     * @return QnAThread
     */
    public function setForum(\ZectranetBundle\Entity\QnAForum $forum = null)
    {
        $this->forum = $forum;

        return $this;
    }

    /**
     * Get forum
     *
     * @return \ZectranetBundle\Entity\QnAForum 
     */
    public function getForum()
    {
        return $this->forum;
    }

    /**
     * @param EntityManager $em
     * @param User $user
     * @param $forum
     * @param $parameters
     * @return QnAThread
     */
    public static function createNewQuestion($em, $user, $forum, $parameters)
    {
        $question = new QnAThread();

        $question->setForum($forum);
        $question->setUser($user);
        $question->setTitle($parameters['title']);
        $question->setKeywords($parameters['keywords']);
        $question->setMessage($parameters['message']);

        $em->persist($question);
        $em->flush();

        return $question;
    }
}
