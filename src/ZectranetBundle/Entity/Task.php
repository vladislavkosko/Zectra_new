<?php

namespace ZectranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ZectranetBundle\Entity\Office;
use ZectranetBundle\Entity\User;

/**
 * Task
 *
 * @ORM\Table(name="tasks")
 * @ORM\Entity
 */
class Task
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
     * @ORM\Column(name="name", type="text")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var integer
     * @ORM\Column(name="progress", type="integer")
     */
    private $progress;

    /**
     * @var integer
     * @ORM\Column(name="estimated_hours", type="integer")
     */
    private $estimatedHours;

    /**
     * @var integer
     * @ORM\Column(name="estimated_minutes", type="integer")
     */
    private $estimatedMinutes;

    /**
     * @var \DateTime
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startdate;

    /**
     * @var \DateTime
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $enddate;

    /**
     * @var integer
     * @ORM\Column(name="type_id", type="integer")
     */
    private $typeid;

    /**
     * @ORM\ManyToOne(targetEntity="TaskType", cascade={"persist"}, inversedBy="tasks")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * @var TaskType
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="parent_id", type="integer")
     */
    private $parentid;

    /**
     * @ORM\ManyToOne(targetEntity="Task")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * @var Task
     */
    protected $parent;

    /**
     * @var integer
     * @ORM\Column(name="project_id", type="integer")
     */
    private $projectid;

    /**
     * @ORM\ManyToOne(targetEntity="Project", cascade={"persist"}, inversedBy="tasks")
     * @ORM\JoinColumn(name="project_id")
     * @var Project
     */
    private $project;

    /**
     * @var integer
     * @ORM\Column(name="assigned_id", type="integer")
     */
    private $assignedid;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="assignedTasks")
     * @ORM\JoinColumn(name="assigned_id", referencedColumnName="id")
     */
    private $assigned;

    /**
     * @var integer
     * @ORM\Column(name="priority_id", type="integer")
     */
    private $priotityid;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="TaskPriority", inversedBy="tasks")
     * @ORM\JoinColumn(name="priority_id", referencedColumnName="id")
     */
    private $priority;

    /**
     * @var integer
     * @ORM\Column(name="status_id", type="integer")
     */
    private $statusid;

    /**
     * @ORM\ManyToOne(targetEntity="TaskStatus", inversedBy="tasks")
     * @ORM\JoinColumn(name="status_id")
     * @var TaskStatus
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="TaskPost", mappedBy="task")
     * @var array
     */
    private $posts;

    /**
     * @var integer
     *
     * @ORM\Column(name="sprint_id", type="integer")
     */
    private $sprintid;

    /**
     * @var Sprint
     * @ORM\ManyToOne(targetEntity="Sprint", inversedBy="tasks")
     * @ORM\JoinColumn(name="sprint_id", referencedColumnName="id")
     */
    private $sprint;

    /**
     * @var integer
     *
     * @ORM\Column(name="owner_id", type="integer")
     */
    private $ownerid;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ownedTasks")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Task
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
     * @return Task
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
     * Set progress
     *
     * @param integer $progress
     * @return Task
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * Get progress
     *
     * @return integer 
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * Set estimatedHours
     *
     * @param integer $estimatedHours
     * @return Task
     */
    public function setEstimatedHours($estimatedHours)
    {
        $this->estimatedHours = $estimatedHours;

        return $this;
    }

    /**
     * Get estimatedHours
     *
     * @return integer 
     */
    public function getEstimatedHours()
    {
        return $this->estimatedHours;
    }

    /**
     * Set estimatedMinutes
     *
     * @param integer $estimatedMinutes
     * @return Task
     */
    public function setEstimatedMinutes($estimatedMinutes)
    {
        $this->estimatedMinutes = $estimatedMinutes;

        return $this;
    }

    /**
     * Get estimatedMinutes
     *
     * @return integer 
     */
    public function getEstimatedMinutes()
    {
        return $this->estimatedMinutes;
    }

    /**
     * Set startdate
     *
     * @param \DateTime $startdate
     * @return Task
     */
    public function setStartdate($startdate)
    {
        $this->startdate = $startdate;

        return $this;
    }

    /**
     * Get startdate
     *
     * @return \DateTime 
     */
    public function getStartdate()
    {
        return $this->startdate;
    }

    /**
     * Set enddate
     *
     * @param \DateTime $enddate
     * @return Task
     */
    public function setEnddate($enddate)
    {
        $this->enddate = $enddate;

        return $this;
    }

    /**
     * Get enddate
     *
     * @return \DateTime 
     */
    public function getEnddate()
    {
        return $this->enddate;
    }

    /**
     * Set typeid
     *
     * @param integer $typeid
     * @return Task
     */
    public function setTypeid($typeid)
    {
        $this->typeid = $typeid;

        return $this;
    }

    /**
     * Get typeid
     *
     * @return integer 
     */
    public function getTypeid()
    {
        return $this->typeid;
    }

    /**
     * Set parentid
     *
     * @param integer $parentid
     * @return Task
     */
    public function setParentid($parentid)
    {
        $this->parentid = $parentid;

        return $this;
    }

    /**
     * Get parentid
     *
     * @return integer 
     */
    public function getParentid()
    {
        return $this->parentid;
    }

    /**
     * Set projectid
     *
     * @param integer $projectid
     * @return Task
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
     * Set assignedid
     *
     * @param integer $assignedid
     * @return Task
     */
    public function setAssignedid($assignedid)
    {
        $this->assignedid = $assignedid;

        return $this;
    }

    /**
     * Get assignedid
     *
     * @return integer 
     */
    public function getAssignedid()
    {
        return $this->assignedid;
    }

    /**
     * Set priotityid
     *
     * @param integer $priotityid
     * @return Task
     */
    public function setPriotityid($priotityid)
    {
        $this->priotityid = $priotityid;

        return $this;
    }

    /**
     * Get priotityid
     *
     * @return integer 
     */
    public function getPriotityid()
    {
        return $this->priotityid;
    }

    /**
     * Set statusid
     *
     * @param integer $statusid
     * @return Task
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
     * Set sprintid
     *
     * @param integer $sprintid
     * @return Task
     */
    public function setSprintid($sprintid)
    {
        $this->sprintid = $sprintid;

        return $this;
    }

    /**
     * Get sprintid
     *
     * @return integer 
     */
    public function getSprintid()
    {
        return $this->sprintid;
    }

    /**
     * Set ownerid
     *
     * @param integer $ownerid
     * @return Task
     */
    public function setOwnerid($ownerid)
    {
        $this->ownerid = $ownerid;

        return $this;
    }

    /**
     * Get ownerid
     *
     * @return integer 
     */
    public function getOwnerid()
    {
        return $this->ownerid;
    }

    /**
     * Set type
     *
     * @param \ZectranetBundle\Entity\TaskType $type
     * @return Task
     */
    public function setType(\ZectranetBundle\Entity\TaskType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \ZectranetBundle\Entity\TaskType 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set parent
     *
     * @param \ZectranetBundle\Entity\Task $parent
     * @return Task
     */
    public function setParent(\ZectranetBundle\Entity\Task $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \ZectranetBundle\Entity\Task 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set project
     *
     * @param \ZectranetBundle\Entity\Project $project
     * @return Task
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
     * Set assigned
     *
     * @param \ZectranetBundle\Entity\User $assigned
     * @return Task
     */
    public function setAssigned(\ZectranetBundle\Entity\User $assigned = null)
    {
        $this->assigned = $assigned;

        return $this;
    }

    /**
     * Get assigned
     *
     * @return \ZectranetBundle\Entity\User 
     */
    public function getAssigned()
    {
        return $this->assigned;
    }

    /**
     * Set priority
     *
     * @param \ZectranetBundle\Entity\TaskPriority $priority
     * @return Task
     */
    public function setPriority(\ZectranetBundle\Entity\TaskPriority $priority = null)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return \ZectranetBundle\Entity\TaskPriority 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set status
     *
     * @param \ZectranetBundle\Entity\TaskStatus $status
     * @return Task
     */
    public function setStatus(\ZectranetBundle\Entity\TaskStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \ZectranetBundle\Entity\TaskStatus 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add posts
     *
     * @param \ZectranetBundle\Entity\TaskPost $posts
     * @return Task
     */
    public function addPost(\ZectranetBundle\Entity\TaskPost $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param \ZectranetBundle\Entity\TaskPost $posts
     */
    public function removePost(\ZectranetBundle\Entity\TaskPost $posts)
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
     * Set sprint
     *
     * @param \ZectranetBundle\Entity\Sprint $sprint
     * @return Task
     */
    public function setSprint(\ZectranetBundle\Entity\Sprint $sprint = null)
    {
        $this->sprint = $sprint;

        return $this;
    }

    /**
     * Get sprint
     *
     * @return \ZectranetBundle\Entity\Sprint 
     */
    public function getSprint()
    {
        return $this->sprint;
    }

    /**
     * Set owner
     *
     * @param \ZectranetBundle\Entity\User $owner
     * @return Task
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
}
