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
     * @ORM\Column(name="project_id", type="integer")
     */
    private $projectid;

    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="sprints")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

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
     * @ORM\OneToMany(targetEntity="SprintPermissions", mappedBy="sprint", cascade={"remove"})
     * @var ArrayCollection
     */
    private $permissions;

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
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->tasks = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->setStatus(SprintStatus::getOpenStatus($em));
        $this->setStatusid(1);
    }

    /**
     * @return array
     */
    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'projectid' => $this->getProjectid(),
            'status' => $this->getStatus()->getInArray(),
            'permissions' => EntityOperations::arrayToJsonArray($this->getPermissions())
        );
    }

    /**
     * @param EntityManager $em
     * @param int $project_id
     * @param array $params
     * @return Sprint
     */
    public static function addNewSprint(EntityManager $em, $project_id, $params) {
        $sprint = new Sprint($em);
        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);
        $sprint->setProject($project);
        $sprint->setName($params['name']);
        $sprint->setDescription($params['description']);
        $em->persist($sprint);
        $em->flush();

        return $sprint;
    }

    /**
     * @param EntityManager $em
     * @param int $sprint_id
     * @param array $task_ids
     * @return array
     */
    public static function addTasksToSprint(EntityManager $em, $sprint_id, $task_ids) {
        $tasks = $em->getRepository('ZectranetBundle:Task')
            ->findBy(array('id' => $task_ids));
        $jsonTasks = array();
        $taskIds = array();
        if (count($tasks) > 0) {
            $sprint = $em->getRepository('ZectranetBundle:Sprint')->find($sprint_id);
            /** @var Task $task */
            foreach ($tasks as $task) {
                $task->setSprint($sprint);
                $em->persist($task);
                $taskIds[] = $task->getId();
            }
            $em->flush();
            $jsonTasks = $em->getRepository('ZectranetBundle:Task')->findBy(array('id' => $taskIds));
            $jsonTasks = EntityOperations::arrayToJsonArray($jsonTasks);
        }
        return $jsonTasks;
    }

    /**
     * Set projectid
     *
     * @param integer $projectid
     * @return Sprint
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
     * Set project
     *
     * @param \ZectranetBundle\Entity\Project $project
     * @return Sprint
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
     * Add permissions
     *
     * @param \ZectranetBundle\Entity\SprintPermissions $permissions
     * @return Sprint
     */
    public function addPermission(\ZectranetBundle\Entity\SprintPermissions $permissions)
    {
        $this->permissions[] = $permissions;

        return $this;
    }

    /**
     * Remove permissions
     *
     * @param \ZectranetBundle\Entity\SprintPermissions $permissions
     */
    public function removePermission(\ZectranetBundle\Entity\SprintPermissions $permissions)
    {
        $this->permissions->removeElement($permissions);
    }

    /**
     * Get permissions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
}
