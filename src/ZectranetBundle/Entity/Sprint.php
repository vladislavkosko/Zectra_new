<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Sprint
 *
 * @ORM\Table(name="sprints")
 * @ORM\Entity
 */
class Sprint
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="sprint")
     * @var array
     */
    private $tasks;

    /**
     * @var integer
     * @ORM\Column(name="office_id", type="integer")
     */
    private $officeid;

    /**
     * @var Office
     * @ORM\ManyToOne(targetEntity="Office", inversedBy="sprints")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;

    /**
     * @var integer
     * @ORM\Column(name="status_id", type="integer")
     */
    private $statusid;

    /**
     * @var SprintStatus
     * @ORM\ManyToOne(targetEntity="SprintStatus", inversedBy="sprint")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

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
     * @return Sprint
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
     * Set description
     *
     * @param string $description
     * @return Sprint
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set statusid
     *
     * @param integer $statusid
     * @return Sprint
     */
    public function setStatusid($statusid)
    {
        $this->statusid = $statusid;

        return $this;
    }

    /**
     * Get statusid
     *
     * @return integer 
     */
    public function getStatusid()
    {
        return $this->statusid;
    }

    /**
     * Add tasks
     *
     * @param \ZectranetBundle\Entity\Task $tasks
     * @return Sprint
     */
    public function addTask(Task $tasks)
    {
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove tasks
     *
     * @param \ZectranetBundle\Entity\Task $tasks
     */
    public function removeTask(Task $tasks)
    {
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Set status
     *
     * @param \ZectranetBundle\Entity\SprintStatus $status
     * @return Sprint
     */
    public function setStatus(SprintStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \ZectranetBundle\Entity\SprintStatus 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set officeid
     *
     * @param integer $officeid
     * @return Sprint
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
     * Set office
     *
     * @param \ZectranetBundle\Entity\Office $office
     * @return Sprint
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
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->tasks = new ArrayCollection();
        $this->setStatus(SprintStatus::getOpenStatus($em));
        $this->setStatusid(1);
    }
}
