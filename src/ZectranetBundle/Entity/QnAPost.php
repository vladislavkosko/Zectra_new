<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * QnAPost
 *
 * @ORM\Table(name="QnA_posts")
 * @ORM\Entity
 */
class QnAPost
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
     * @var int
     * @ORM\Column(name="thread_id", type="integer")
     */
    private $threadID;

    /**
     * @var HFThread
     * @ORM\ManyToOne(targetEntity="QnAThread", inversedBy="posts")
     * @ORM\JoinColumn(name="thread_id", referencedColumnName="id")
     */
    private $thread;

    /**
     * @var string
     * @ORM\Column(name="message", type="string", length=3000)
     */
    private $message;

    /**
     * @var \DateTime
     * @ORM\Column(name="posted", type="datetime")
     */
    private $posted;

    /**
     * @var \DateTime
     * @ORM\Column(name="edited", type="datetime", nullable=true, options={"default" = null})
     */
    private $edited;

    /**
     * @return array
     */
    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'threadID' => $this->getThreadID(),
            'message' => $this->getMessage(),
            'userID' => $this->getUserID(),
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
     * Set userID
     *
     * @param integer $userID
     * @return QnAPost
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
     * Set threadID
     *
     * @param integer $threadID
     * @return QnAPost
     */
    public function setThreadID($threadID)
    {
        $this->threadID = $threadID;

        return $this;
    }

    /**
     * Get threadID
     *
     * @return integer 
     */
    public function getThreadID()
    {
        return $this->threadID;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return QnAPost
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
     * Set posted
     *
     * @param \DateTime $posted
     * @return QnAPost
     */
    public function setPosted($posted)
    {
        $this->posted = $posted;

        return $this;
    }

    /**
     * Get posted
     *
     * @return \DateTime 
     */
    public function getPosted()
    {
        return $this->posted;
    }

    /**
     * Set edited
     *
     * @param \DateTime $edited
     * @return QnAPost
     */
    public function setEdited($edited)
    {
        $this->edited = $edited;

        return $this;
    }

    /**
     * Get edited
     *
     * @return \DateTime 
     */
    public function getEdited()
    {
        return $this->edited;
    }

    /**
     * Set user
     *
     * @param \ZectranetBundle\Entity\User $user
     * @return QnAPost
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
     * Set thread
     *
     * @param \ZectranetBundle\Entity\QnAThread $thread
     * @return QnAPost
     */
    public function setThread(\ZectranetBundle\Entity\QnAThread $thread = null)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get thread
     *
     * @return \ZectranetBundle\Entity\QnAThread 
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->posted = new \DateTime();
        $this->edited = null;
    }

    /**
     * @param EntityManager $em
     * @param QnAThread $thread
     * @param User $user
     * @param $message
     * @return QnAPost
     */
    public static function addPost($em, $thread, $user, $message)
    {
        $post = new QnAPost();
        $post->setMessage($message);
        $post->setThread($thread);
        $post->setUser($user);
        $em->persist($post);
        $em->flush();

        return $post;
    }
}
