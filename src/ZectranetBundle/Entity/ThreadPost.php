<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Thread
 *
 * @ORM\Table(name="header_forum_thread_posts")
 * @ORM\Entity
 */
class ThreadPost
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
     * @var Thread
     * @ORM\ManyToOne(targetEntity="Thread", inversedBy="posts")
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
     * @ORM\Column(name="edited", type="datetime")
     */
    private $edited;

    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'message' => $this->getMessage(),
            'userID' => $this->getUserID(),
            'posted' => $this->getPosted()->format('Y-m-d'),
            'edited' => $this->getEdited()->format('Y-m-d'),
            'threadID' => $this->getThreadID(),
        );
    }

    /**
     * @param EntityManager $em
     * @param int $thread_id
     * @param int $user_id
     * @param string $message
     * @return ThreadPost
     */
    public static function addNewPost(EntityManager $em, $thread_id, $user_id, $message) {
        $thread = $em->getRepository('ZectranetBundle:Thread')->find($thread_id);
        $user = $em->getRepository('ZectranetBundle:User')->find($user_id);
        $post = new ThreadPost();
        $post->setMessage($message);
        $post->setThread($thread);
        $post->setUser($user);
        $em->persist($post);
        $em->flush();

        return $post;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->posted = new \DateTime();
        $this->edited = new \DateTime();
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
     * @return ThreadPost
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
     * @return ThreadPost
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
     * @return ThreadPost
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
     * @return ThreadPost
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
     * @return ThreadPost
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
     * @return ThreadPost
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
     * @param \ZectranetBundle\Entity\Thread $thread
     * @return ThreadPost
     */
    public function setThread(\ZectranetBundle\Entity\Thread $thread = null)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get thread
     *
     * @return \ZectranetBundle\Entity\Thread 
     */
    public function getThread()
    {
        return $this->thread;
    }
}
