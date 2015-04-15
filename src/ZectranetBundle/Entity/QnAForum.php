<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * QnAForum
 *
 * @ORM\Table(name="QnA_forums")
 * @ORM\Entity
 */
class QnAForum
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     * @ORM\Column(name="onwer_id", type="integer")
     */
    private $ownerID;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="onwer_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="QnAForums", fetch="EXTRA_LAZY")
     * @var ArrayCollection
     */
    private $users;

    /**
     * @var int
     * @ORM\Column(name="office_id", type="integer")
     */
    private $officeID;

    /**
     * @var Office
     * @ORM\ManyToOne(targetEntity="Office", inversedBy="QnAForums")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QnAThread", mappedBy="forum", cascade={"remove"})
     */
    private $threads;

    /**
     * @var boolean
     * @ORM\Column(name="shared", type="boolean", options={"default" = false})
     */
    private $shared;

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * Constructor
     */
    public function __construct() {
        $this->users = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return QnAForum
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set ownerID
     *
     * @param integer $ownerID
     * @return QnAForum
     */
    public function setOwnerID($ownerID)
    {
        $this->ownerID = $ownerID;

        return $this;
    }

    /**
     * Get ownerID
     *
     * @return integer 
     */
    public function getOwnerID()
    {
        return $this->ownerID;
    }

    /**
     * Set officeID
     *
     * @param integer $officeID
     * @return QnAForum
     */
    public function setOfficeID($officeID)
    {
        $this->officeID = $officeID;

        return $this;
    }

    /**
     * Get officeID
     *
     * @return integer 
     */
    public function getOfficeID()
    {
        return $this->officeID;
    }

    /**
     * Set shared
     *
     * @param boolean $shared
     * @return QnAForum
     */
    public function setShared($shared)
    {
        $this->shared = $shared;

        return $this;
    }

    /**
     * Get shared
     *
     * @return boolean 
     */
    public function getShared()
    {
        return $this->shared;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return QnAForum
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set owner
     *
     * @param \ZectranetBundle\Entity\User $owner
     * @return QnAForum
     */
    public function setOwner(\ZectranetBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \ZectranetBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add users
     *
     * @param \ZectranetBundle\Entity\User $users
     * @return QnAForum
     */
    public function addUser(\ZectranetBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \ZectranetBundle\Entity\User $users
     */
    public function removeUser(\ZectranetBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set office
     *
     * @param \ZectranetBundle\Entity\Office $office
     * @return QnAForum
     */
    public function setOffice(\ZectranetBundle\Entity\Office $office = null)
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
     * Add threads
     *
     * @param \ZectranetBundle\Entity\QnAThread $threads
     * @return QnAForum
     */
    public function addThread(\ZectranetBundle\Entity\QnAThread $threads)
    {
        $this->threads[] = $threads;

        return $this;
    }

    /**
     * Remove threads
     *
     * @param \ZectranetBundle\Entity\QnAThread $threads
     */
    public function removeThread(\ZectranetBundle\Entity\QnAThread $threads)
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
