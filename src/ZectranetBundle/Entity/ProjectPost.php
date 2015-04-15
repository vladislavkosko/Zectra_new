<?php

namespace ZectranetBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectPost
 *
 * @ORM\Table(name="project_posts")
 * @ORM\Entity
 */
class ProjectPost
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
     * @ORM\Column(name="edited", type="datetime", nullable=true)
     */
    private $edited;

    /**
     * @var integer
     *
     * @ORM\Column(name="project_id", type="integer")
     */
    private $projectid;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="postsProject")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userid;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="postsProject")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
     * Set message
     *
     * @param string $message
     * @return ProjectPost
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
     * @return ProjectPost
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
     * @return ProjectPost
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
     * Set projectid
     *
     * @param integer $projectid
     * @return ProjectPost
     */
    public function setProjectid($projectid)
    {
        $this->projectid = $projectid;

        return $this;
    }

    /**
     * Get projectid
     *
     * @return integer 
     */
    public function getProjectid()
    {
        return $this->projectid;
    }

    /**
     * Set userid
     *
     * @param integer $userid
     * @return ProjectPost
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
     * Set project
     *
     * @param \ZectranetBundle\Entity\Project $project
     * @return ProjectPost
     */
    public function setProject(\ZectranetBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \ZectranetBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set user
     *
     * @param \ZectranetBundle\Entity\User $user
     * @return ProjectPost
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
     * @param int $user_id
     * @param int $project_id
     * @param string $message
     * @return ProjectPost
     */
    public static function addNewPost(EntityManager $em, $user_id, $project_id, $message) {
        $user = $em->getRepository('ZectranetBundle:User')->find($user_id);
        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);

        $post = new ProjectPost();
        $post->setUser($user);
        $post->setEdited(null);
        $post->setMessage($message);
        $post->setProject($project);
        $post->setPosted(new \DateTime());

        $em->persist($post);
        $em->flush();
        return $post;
    }

    /**
     * @param EntityManager $em
     * @param int $post_id
     * @param string $message
     * @return ProjectPost
     */
    public static function EditPost(EntityManager $em, $post_id,$message) {
        $post = $em->getRepository('ZectranetBundle:ProjectPost')->find($post_id);

        $post->setEdited(new \DateTime());
        $post->setMessage($message);

        $em->persist($post);
        $em->flush();
        return $post;
    }

    public function getInArray() {
        return array(
            'id' => $this->id,
            'message' => $this->message,
            'posted' => $this->posted->format('Y-m-d H:i:s'),
            'edited' => ($this->edited) ? $this->edited-> format('Y-m-d H:i:s') : null,
            'projectid' => $this->projectid,
            'userid' =>  $this->userid,
            'user' => $this->getUser()->getInArray()
        );
    }

    /**
     * @param EntityManager $em
     * @param int $project_id
     * @param int $offset
     * @param int $count
     * @return array
     */
    public static function getPostsOffset(EntityManager $em, $project_id, $offset, $count) {
        return $em->getRepository('ZectranetBundle:ProjectPost')
            ->findBy(array('projectid' => $project_id), array('id' => 'DESC'), $count, $offset);
    }
}
