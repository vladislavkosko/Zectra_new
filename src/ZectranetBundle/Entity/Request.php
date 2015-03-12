<?php

namespace ZectranetBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\Office;
use ZectranetBundle\Entity\User;

/**
 * Request
 *
 * @ORM\Table(name="requests")
 * @ORM\Entity
 */
class Request
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
     * @var integer
     * @ORM\Column(name="type_id", type="integer")
     */
    private $typeid;

    /**
     * @ORM\ManyToOne(targetEntity="RequestType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * @var RequestType
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userid;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="requests")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="project_id", type="integer", nullable=true)
     */
    private $projectid;

    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @var integer
     *
     * @ORM\Column(name="office_id", type="integer", nullable=true)
     */
    private $officeid;

    /**
     * @var Office
     * @ORM\ManyToOne(targetEntity="Office")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;

    /**
     * @var integer
     *
     * @ORM\Column(name="task_id", type="integer", nullable=true)
     */
    private $taskid;

    /**
     * @var Task
     * @ORM\ManyToOne(targetEntity="Task")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id")
     */
    private $task;

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
     * Set typeid
     *
     * @param integer $typeid
     * @return Request
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
     * Set userid
     *
     * @param integer $userid
     * @return Request
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
     * Set projectid
     *
     * @param integer $projectid
     * @return Request
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
     * Set officeid
     *
     * @param integer $officeid
     * @return Request
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
     * Set type
     *
     * @param \ZectranetBundle\Entity\RequestType $type
     * @return Request
     */
    public function setType(\ZectranetBundle\Entity\RequestType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \ZectranetBundle\Entity\RequestType 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set user
     *
     * @param \ZectranetBundle\Entity\User $user
     * @return Request
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
     * Set project
     *
     * @param \ZectranetBundle\Entity\Project $project
     * @return Request
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
     * Set office
     *
     * @param \ZectranetBundle\Entity\Office $office
     * @return Request
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
     * Set taskid
     *
     * @param integer $taskid
     * @return Request
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
     * Set task
     *
     * @param \ZectranetBundle\Entity\Task $task
     * @return Request
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
     * @param EntityManager $em
     * @param User $user
     * @param RequestType $type
     * @param Project $project
     */
    public static function addRequestUserProject($em, $user, $type, $project)
    {
        /** @var Request $request_user_project */
        $request_user_project = new Request();

        $request_user_project->setType($type);
        $request_user_project->setUser($user);
        $request_user_project->setProject($project);

        $em->persist($request_user_project);
        $em->flush();
    }
}
