<?php

namespace ZectranetBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * TaskPost
 *
 * @ORM\Table(name="posts_task")
 * @ORM\Entity
 */
class TaskPost
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(name="task_id", type="integer")
     */
    private $taskid;

    /**
     * @ORM\ManyToOne(targetEntity="Task", inversedBy="posts")
     * @ORM\JoinColumn(name="task_id")
     * @var Task
     */
    private $task;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userid;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="postsTask")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="posted", type="datetime")
     */
    private $posted;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="edited", type="datetime", nullable=true, options={"defaul" = null})
     */
    private $edited;

    public function __construct() {
        $this->posted = new \DateTime();
        $this->edited = null;
    }

    /**
     * Get inArray
     *
     * @return array
     */
    public function getInArray()
    {
        return array(
            'id' => $this->getId(),
            'taskid' => $this->getTaskid(),
            'userid' => $this->getUserid(),
            'user' => $this->getUser()->getInArray(),
            'message' => $this->getMessage(),
            'posted' => $this->getPosted()->format('Y-m-d H:i:s'),
            'edited' => ($this->getEdited() != null) ? $this->getEdited()->format('Y-m-d H:i:s') : null,
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
     * Set taskid
     *
     * @param integer $taskid
     * @return TaskPost
     */
    public function setTaskid($taskid)
    {
        $this->taskid = $taskid;

        return $this;
    }

    /**
     * Get taskid
     *
     * @return integer 
     */
    public function getTaskid()
    {
        return $this->taskid;
    }

    /**
     * Set userid
     *
     * @param integer $userid
     * @return TaskPost
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return integer 
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return TaskPost
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
     * @return TaskPost
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
     * @return TaskPost
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
     * Set task
     *
     * @param \ZectranetBundle\Entity\Task $task
     * @return TaskPost
     */
    public function setTask(\ZectranetBundle\Entity\Task $task = null)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return \ZectranetBundle\Entity\Task 
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set user
     *
     * @param \ZectranetBundle\Entity\User $user
     * @return TaskPost
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
     * @param EntityManager $em
     * @param int $taskid
     * @param int $offset
     * @param int $count
     * @return array
     */
    public static function getPostsOffset(EntityManager $em, $taskid, $offset, $count) {
        return $em->getRepository('ZectranetBundle:TaskPost')
            ->findBy(array('taskid' => $taskid), array('id' => 'DESC'), $count, $offset);
    }

    /**
     * @param EntityManager $em
     * @param int $user_id
     * @param int $taskid
     * @param string $message
     * @return OfficePost
     */
    public static function addNewPost(EntityManager $em, $user_id, $taskid, $message) {
        $user = $em->getRepository('ZectranetBundle:User')->find($user_id);
        $task = $em->getRepository('ZectranetBundle:Task')->find($taskid);

        $post = new TaskPost();
        $post->setUser($user);
        $post->setEdited(null);
        $post->setMessage($message);
        $post->setTask($task);

        $em->persist($post);
        $em->flush();

        return $post;
    }

    public static function editMessage(EntityManager $em, $message_id, $message) {

        $post = $em->getRepository('ZectranetBundle:TaskPost')->find($message_id);

        $post->setEdited(new \DateTime());
        $post->setMessage($message);

        $em->persist($post);
        $em->flush();
        return $post;
    }
}
