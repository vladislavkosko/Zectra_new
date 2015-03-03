<?php

namespace ZectranetBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * OfficePost
 *
 * @ORM\Table(name="office_posts")
 * @ORM\Entity
 */
class OfficePost
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
     * @ORM\Column(name="office_id", type="integer")
     */
    private $officeid;

    /**
     * @ORM\ManyToOne(targetEntity="Office", inversedBy="postsOffice")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userid;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="postsOffice")
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
     * @return OfficePost
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
     * @return OfficePost
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
     * @return OfficePost
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
     * Set officeid
     *
     * @param integer $officeid
     * @return OfficePost
     */
    public function setOfficeid($officeid)
    {
        $this->officeid = $officeid;

        return $this;
    }

    /**
     * Get officeid
     *
     * @return integer 
     */
    public function getOfficeid()
    {
        return $this->officeid;
    }

    /**
     * Set userid
     *
     * @param integer $userid
     * @return OfficePost
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
     * Set office
     *
     * @param \ZectranetBundle\Entity\Office $office
     * @return OfficePost
     */
    public function setOffice(Office $office = null)
    {
        $this->office = $office;

        return $this;
    }

    /**
     * Get office
     *
     * @return \ZectranetBundle\Entity\Office 
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * Set user
     *
     * @param \ZectranetBundle\Entity\User $user
     * @return OfficePost
     */
    public function setUser(User $user = null)
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
     * @param int $office_id
     * @param string $message
     * @return OfficePost
     */
    public static function addNewPost(EntityManager $em, $user_id, $office_id, $message) {
        $user = $em->getRepository('ZectranetBundle:User')->find($user_id);
        $office = $em->getRepository('ZectranetBundle:Office')->find($office_id);

        $post = new OfficePost();
        $post->setUser($user);
        $post->setEdited(null);
        $post->setMessage($message);
        $post->setOffice($office);
        $post->setPosted(new \DateTime());

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
            'officeid' => $this->officeid,
            'userid' =>  $this->userid,
            'user' => $this->getUser()->getInArray()
        );
    }

    /**
     * @param EntityManager $em
     * @param int $office_id
     * @param int $offset
     * @param int $count
     * @return array
     */
    public static function getPostsOffset(EntityManager $em, $office_id, $offset, $count) {
        return $em->getRepository('ZectranetBundle:OfficePost')
            ->findBy(array('officeid' => $office_id), array('id' => 'DESC'), $count, $offset);
    }
}
