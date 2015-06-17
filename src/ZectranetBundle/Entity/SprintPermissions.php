<?php
namespace ZectranetBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * SprintPermissions
 *
 * @ORM\Table(name="sprint_permissions")
 * @ORM\Entity
 */
class SprintPermissions
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
     * @ORM\Column(name="sprint_id", type="integer")
     */
    private $sprintid;

    /**
     * @var Sprint
     * @ORM\ManyToOne(targetEntity="Sprint", inversedBy="permissions")
     * @ORM\JoinColumn(name="sprint_id", referencedColumnName="id")
     */
    private $sprint;

    /**
     * @var integer
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userid;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userPermissions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @var User
     */
    protected $user;

    /**
     * @var boolean
     * @ORM\Column(name="enable_add_task_to_sprint", type="boolean", options={"default" = false})
     */
    private $enableAddTaskToSprint;

    /**
     * @var boolean
     * @ORM\Column(name="enable_start_sprint", type="boolean", options={"default" = false})
     */
    private $enableStartSprint;

    /**
     * @var boolean
     * @ORM\Column(name="enable_change_task_status_to_signed_off", type="boolean", options={"default" = false})
     */
    private $enableChangeTaskStatusToSignedOff;

    /**
     * @var boolean
     * @ORM\Column(name="enable_add_subtask_bug", type="boolean", options={"default" = false})
     */
    private $enableAddSubtaskBug;

    function __construct()
    {
        $this->enableAddTaskToSprint = false;
        $this->enableStartSprint = false;
        $this->enableChangeTaskStatusToSignedOff = false;
        $this->enableAddSubtaskBug = false;
    }

    public function getInArray()
    {
        return array(
            'id' => $this->getId(),
            'sprintid' => $this->getSprintid(),
            'userid' => $this->getUserid(),
            'enableAddTaskToSprint' => $this->getEnableAddTaskToSprint(),
            'enableStartSprint' => $this->getEnableStartSprint(),
            'enableChangeTaskStatusToSignedOff' => $this->getEnableChangeTaskStatusToSignedOff(),
            'enableAddSubtaskBug' => $this->getEnableAddSubtaskBug()
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
     * Set sprintid
     *
     * @param integer $sprintid
     * @return SprintPermissions
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
     * Set sprint
     *
     * @param \ZectranetBundle\Entity\Sprint $sprint
     * @return SprintPermissions
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
     * Set userid
     *
     * @param integer $userid
     * @return SprintPermissions
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
     * Set enableAddTaskToSprint
     *
     * @param boolean $enableAddTaskToSprint
     * @return SprintPermissions
     */
    public function setEnableAddTaskToSprint($enableAddTaskToSprint)
    {
        $this->enableAddTaskToSprint = $enableAddTaskToSprint;

        return $this;
    }

    /**
     * Get enableAddTaskToSprint
     *
     * @return boolean 
     */
    public function getEnableAddTaskToSprint()
    {
        return $this->enableAddTaskToSprint;
    }

    /**
     * Set enableStartSprint
     *
     * @param boolean $enableStartSprint
     * @return SprintPermissions
     */
    public function setEnableStartSprint($enableStartSprint)
    {
        $this->enableStartSprint = $enableStartSprint;

        return $this;
    }

    /**
     * Get enableStartSprint
     *
     * @return boolean 
     */
    public function getEnableStartSprint()
    {
        return $this->enableStartSprint;
    }

    /**
     * Set enableChangeTaskStatusToSignedOff
     *
     * @param boolean $enableChangeTaskStatusToSignedOff
     * @return SprintPermissions
     */
    public function setEnableChangeTaskStatusToSignedOff($enableChangeTaskStatusToSignedOff)
    {
        $this->enableChangeTaskStatusToSignedOff = $enableChangeTaskStatusToSignedOff;

        return $this;
    }

    /**
     * Get enableChangeTaskStatusToSignedOff
     *
     * @return boolean 
     */
    public function getEnableChangeTaskStatusToSignedOff()
    {
        return $this->enableChangeTaskStatusToSignedOff;
    }

    /**
     * Set enableAddSubtaskBug
     *
     * @param boolean $enableAddSubtaskBug
     * @return SprintPermissions
     */
    public function setEnableAddSubtaskBug($enableAddSubtaskBug)
    {
        $this->enableAddSubtaskBug = $enableAddSubtaskBug;

        return $this;
    }

    /**
     * Get enableAddSubtaskBug
     *
     * @return boolean 
     */
    public function getEnableAddSubtaskBug()
    {
        return $this->enableAddSubtaskBug;
    }

    /**
     * Set user
     *
     * @param \ZectranetBundle\Entity\User $user
     * @return SprintPermissions
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
     * @param $sprints
     * @param User $user
     */
    public static function addPermission($em, $sprints, $user)
    {

        /** @var Sprint $sprint */
        foreach($sprints as $sprint)
        {
            /** @var SprintPermissions $permission */
            $permission = new SprintPermissions();
            $permission->setSprint($sprint);
            $permission->setUser($user);

            $em->persist($permission);
        }

        $em->flush();
    }

    /**
     * @param EntityManager $em
     * @param Sprint $sprints
     * @param $user
     */
    public static function addPermission1($em, $sprint, $users)
    {

        /** @var User $user */
        foreach($users as $user)
        {
            /** @var SprintPermissions $permission */
            $permission = new SprintPermissions();
            $permission->setSprint($sprint);
            $permission->setUser($user);

            $em->persist($permission);
        }

        $em->flush();
    }

    /**
     * @param EntityManager $em
     * @param Sprint $sprint
     * @param User $user
     * @param array $parameters
     */
    public static function savePermission($em, $sprint, $user, $parameters)
    {
        $permission = $em->getRepository('ZectranetBundle:SprintPermissions')->findOneBy(array(
            'sprintid' => $sprint->getId(),
            'userid' => $user->getId()
        ));

        $permission->setEnableAddTaskToSprint(($parameters['enableAddTaskToSprint'] == null) ? false : true);
        $permission->setEnableStartSprint(($parameters['enableStartSprint'] == null) ? false : true);
        $permission->setEnableChangeTaskStatusToSignedOff(($parameters['enableChangeTaskStatusToSignedOff'] == null) ? false : true);
        $permission->setEnableAddSubtaskBug(($parameters['enableAddSubtaskBug'] == null) ? false : true);

        $em->flush();
    }
}
