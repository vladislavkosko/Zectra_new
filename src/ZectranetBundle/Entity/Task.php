<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use ZectranetBundle\Entity\Office;
use ZectranetBundle\Entity\User;
use ZectranetBundle\Entity\TaskStatus;
use ZectranetBundle\Services\TaskLogger;

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
     * @ORM\Column(name="parent_id", type="integer", nullable=true, options={"default"=null})
     */
    private $parentid;

    /**
     * @ORM\ManyToOne(targetEntity="Task", inversedBy="subtasks")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * @var Task
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="parent", cascade={"remove"})
     * @var ArrayCollection
     */
    private $subtasks;

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
     * @ORM\Column(name="version_id", type="integer", nullable=true, options={"default" = null})
     */
    private $versionid;

    /**
     * @ORM\ManyToOne(targetEntity="Version", inversedBy="tasks")
     * @ORM\JoinColumn(name="version_id")
     * @var Version
     */
    private $version;

    /**
     * @var integer
     * @ORM\Column(name="assigned_id", type="integer", nullable=true, options={"default"=null})
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
     * @ORM\OneToMany(targetEntity="TaskPost", mappedBy="task", cascade={"remove"})
     * @var array
     */
    private $posts;

    /**
     * @var integer
     *
     * @ORM\Column(name="sprint_id", type="integer", nullable=true, options={"default"=null})
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
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TaskLog", mappedBy="task", cascade={"remove"})
     * @ORM\OrderBy({"date" = "DESC"})
     */
    private $logs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->progress = 0;
        $this->estimatedHours = 0;
        $this->estimatedMinutes = 0;
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->versionid = null;
        $this->subtasks = new ArrayCollection();
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

    /**
     * Add subtasks
     *
     * @param \ZectranetBundle\Entity\Notification $subtasks
     * @return Task
     */
    public function addSubtask(Notification $subtasks)
    {
        $this->subtasks[] = $subtasks;

        return $this;
    }

    /**
     * Remove subtasks
     * @param \ZectranetBundle\Entity\Notification $subtasks
     */
    public function removeSubtask(Notification $subtasks)
    {
        $this->subtasks->removeElement($subtasks);
    }

    /**
     * Get subtasks
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubtasks()
    {
        return $this->subtasks;
    }

    /**
     * Add logs
     *
     * @param \ZectranetBundle\Entity\TaskLog $logs
     * @return Task
     */
    public function addLog(\ZectranetBundle\Entity\TaskLog $logs)
    {
        $this->logs[] = $logs;

        return $this;
    }

    /**
     * Remove logs
     *
     * @param \ZectranetBundle\Entity\TaskLog $logs
     */
    public function removeLog(\ZectranetBundle\Entity\TaskLog $logs)
    {
        $this->logs->removeElement($logs);
    }

    /**
     * Get logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * @param EntityManager $em
     * @param User $user
     * @param int $project_id
     * @param array $parameters
     * @return Task
     */
    public static function addNewTask(EntityManager $em, User $user, $project_id, $parameters) {
        $task = new Task();
        $task->setName($parameters['name']);
        $task->setDescription($parameters['description']);
        $task->setProject($em->getRepository('ZectranetBundle:Project')->find($project_id));
        $task->setStatus($em->getRepository('ZectranetBundle:TaskStatus')->find(1));
        $task->setOwner($user);
        $task->setPriority($em->getRepository('ZectranetBundle:TaskPriority')->find($parameters['priority']));
        $task->setType($em->getRepository('ZectranetBundle:TaskType')->find($parameters['type']));
        $task->setStartdate(\DateTime::createFromFormat('Y-m-d', $parameters['startdate']));
        $task->setEnddate(\DateTime::createFromFormat('Y-m-d', $parameters['enddate']));

        $em->persist($task);
        $em->flush();

        return $task;
    }

    /**
     * @param EntityManager $em
     * @param User $user
     * @param int $project_id
     * @param array $parameters
     * @return Task
     */
    public static function addNewSubTask(EntityManager $em, User $user, $project_id, $parameters) {
        $parent = $em->getRepository('ZectranetBundle:Task')->find($parameters['parent']);
        $task = new Task();
        $task->setName($parameters['name']);
        $task->setDescription($parameters['description']);
        $task->setProject($parent->getProject());
        $task->setStatus($em->getRepository('ZectranetBundle:TaskStatus')->find(1));
        $task->setOwner($user);
        $task->setPriority($em->getRepository('ZectranetBundle:TaskPriority')->find($parameters['priority']));
        $task->setType($em->getRepository('ZectranetBundle:TaskType')->find($parameters['type']));
        $task->setStartdate(\DateTime::createFromFormat('Y-m-d', $parameters['startdate']));
        $task->setEnddate(\DateTime::createFromFormat('Y-m-d', $parameters['enddate']));
        $task->setParent($parent);

        $em->persist($task);
        $em->flush();

        return $task;
    }

    /**
     * @param EntityManager $em
     * @param int $task_id
     */
    public static function deleteTask(EntityManager $em, $task_id) {
        $task = $em->getRepository('ZectranetBundle:Task')->find($task_id);
        if (count($task->getSubtasks()) > 0) {
            /** @var Task $subtask */
            foreach ($task->getSubtasks() as $subtask) {
                $em->remove($subtask);
            }
        }
        $em->remove($task);
        $em->flush();
    }

    /**
     * @param EntityManager $em
     * @param TaskLogger $logger
     * @param int $task_id
     * @param string $description
     * @return Task
     */
    public static function editTaskDescription(EntityManager $em, TaskLogger $logger, $task_id, $description) {
        $task = $em->getRepository('ZectranetBundle:Task')->find($task_id);
        $logger->valueChanged(1, $task_id, $task->getDescription(), $description);
        $task->setDescription($description);
        $em->persist($task);
        $em->flush();

        return $task;
    }

    /**
     * @param EntityManager $em
     * @param TaskLogger $logger
     * @param int $task_id
     * @param array $parameters
     * @return Task
     */
    public static function editInfo(EntityManager $em, TaskLogger $logger, $task_id, $parameters) {
        $task = $em->getRepository('ZectranetBundle:Task')->find($task_id);
        $name = $parameters['name'];
        $type_id = $parameters['type'];
        $priority_id = $parameters['priority'];
        $status_id = $parameters['status'];
        $project_id = $parameters['project'];
        $assigned_id = $parameters['assigned'];
        $progress = $parameters['progress'];
        $estimatedHours = $parameters['estimated_hours'];
        $estimatedMinutes = $parameters['estimated_minutes'];
        $startDate = $parameters['start_date'];
        $endDate = $parameters['end_date'];
        $version = $parameters['version'];

        if ($name !== $task->getName()) {
            $logger->valueChanged(0, $task_id, $task->getName(), $name);
            $task->setName($name);
        }

        if ($type_id != $task->getTypeid()) {
            $type = $em->getRepository('ZectranetBundle:TaskType')->find($type_id);
            $logger->valueChanged(2, $task_id, $task->getType()->getLabel(), $type->getLabel());
            $task->setType($type);
        }

        if ($priority_id != $task->getPriotityid()) {
            $priority = $em->getRepository('ZectranetBundle:TaskPriority')->find($priority_id);
            $logger->valueChanged(3, $task_id, $task->getPriority()->getLabel(), $priority->getLabel());
            $task->setPriority($priority);
        }

        if ($status_id != $task->getStatusid()) {
            $status = $em->getRepository('ZectranetBundle:TaskStatus')->find($status_id);
            $logger->valueChanged(4, $task_id, $task->getStatus()->getLabel(), $status->getLabel());
            $task->setStatus($status);
        }

        if ($project_id != $task->getProjectid()) {
            $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);
            $logger->valueChanged(5, $task_id, $task->getProject()->getName(), $project->getName());
            $task->setProject($project);
        }

        if ($assigned_id != $task->getAssignedid()) {
            $assigned = $em->getRepository('ZectranetBundle:User')->find($assigned_id);
            $logger->valueChanged(6, $task_id,
                ($task->getAssigned())
                    ? $task->getAssigned()->getName()
                    : 'Not Assigned',
                ($assigned)
                    ? $assigned->getName()
                    : 'Not Assigned'
            );
            $task->setAssigned($assigned);
        }

        if ($progress != $task->getProgress()) {
            $logger->valueChanged(7, $task_id, $task->getProgress() . '%', $progress . '%');
            $task->setProgress($progress);
        }

        if ($estimatedHours != $task->getEstimatedHours()) {
            $logger->valueChanged(8, $task_id, $task->getEstimatedHours() . ' h', $estimatedHours . ' h');
            $task->setEstimatedHours($estimatedHours);
        }

        if ($estimatedMinutes != $task->getEstimatedMinutes()) {
            $logger->valueChanged(9, $task_id, $task->getEstimatedMinutes() . ' m', $estimatedMinutes . ' m');
            $task->setEstimatedMinutes($estimatedMinutes);
        }

        if ($startDate !== $task->getStartdate()->format('Y-m-d')) {
            $logger->valueChanged(10, $task_id, $task->getStartdate()->format('Y-m-d'), $startDate);
            $task->setStartdate(\DateTime::createFromFormat('Y-m-d', $startDate));
        }

        if ($endDate !== $task->getEnddate()->format('Y-m-d')) {
            $logger->valueChanged(11, $task_id, $task->getEnddate()->format('Y-m-d'), $endDate);
            $task->setEnddate(\DateTime::createFromFormat('Y-m-d', $endDate));
        }

        if ($version != $task->getVersionid()) {
            $version = $em->getRepository('ZectranetBundle:Version')->find($version);
            $logger->valueChanged(12, $task_id, (
                $task->getVersionid())
                    ? $task->getVersion()->getName()
                    : '-',
                $version->getName()
            );
            $task->setVersion($version);
        }

        $em->persist($task);
        $em->flush();
        return $task;
    }

    public static function arrayToJson($array) {
        $jsonArray = array();
        /** @var Task $task */
        foreach ($array as $task) {
            $jsonArray[] = $task->getInArray();
        }
        return $jsonArray;
    }

    /**
     * @return array
     */
    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'parentid' => ($this->getParent())
                ? $this->getParent()->getId()
                : null,
            'projectid' => ($this->getProject())
                ? $this->getProject()->getId()
                : null,
            'progress' => $this->getProgress(),
            'assigned' => ($this->assigned) ? $this->getAssigned()->getInArray() : null,
            'assignedid' => $this->getAssignedid(),
            'startDate' => $this->getStartdate()->format('Y-m-d'),
            'endDate' => $this->getEnddate()->format('Y-m-d'),
            'estimatedHours' => $this->getEstimatedHours(),
            'estimatedMinutes' => $this->getEstimatedMinutes(),
            'owner' => $this->getOwner()->getInArray(),
            'status' => $this->getStatus()->getInArray(),
            'type' => $this->getType()->getInArray(),
            'priority' => $this->getPriority()->getInArray(),
            'subtasks' => EntityOperations::arrayToJsonArray($this->getSubtasks()),
            'sprint' => ($this->getSprint() || $this->getSprintid()) ? $this->getSprint()->getInArray() : null,
            'postCount' => count($this->getPosts()),
            'versionid' => $this->getVersionid(),
            'sprintID' => ($this->getParentid())
                ? $this->getParent()->getSprintid()
                : $this->getSprintid(),
        );
    }

    /**
     * Set versionid
     *
     * @param integer $versionid
     * @return Task
     */
    public function setVersionid($versionid)
    {
        $this->versionid = $versionid;

        return $this;
    }

    /**
     * Get versionid
     *
     * @return integer 
     */
    public function getVersionid()
    {
        return $this->versionid;
    }

    /**
     * Set version
     *
     * @param \ZectranetBundle\Entity\Version $version
     * @return Task
     */
    public function setVersion(\ZectranetBundle\Entity\Version $version = null)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return \ZectranetBundle\Entity\Version 
     */
    public function getVersion()
    {
        return $this->version;
    }
}
