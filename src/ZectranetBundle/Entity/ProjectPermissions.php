<?php

namespace ZectranetBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectPermissions
 *
 * @ORM\Table(name="project_permissions")
 * @ORM\Entity
 */
class ProjectPermissions
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
     * @ORM\Column(name="project_id", type="integer")
     */
    private $projectid;

    /**
     * @var Sprint
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="projectPermissions")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @var integer
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userid;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userProjectPermissions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @var User
     */
    protected $user;

    /**
     * @var boolean
     * @ORM\Column(name="enable_create_sprint", type="boolean", options={"default" = false})
     */
    private $enableCreateSprint;

    function __construct()
    {
        $this->enableCreateSprint = false;
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
     * Set projectid
     *
     * @param integer $projectid
     * @return ProjectPermissions
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
     * @return ProjectPermissions
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
     * Set enableCreateSprint
     *
     * @param boolean $enableCreateSprint
     * @return ProjectPermissions
     */
    public function setEnableCreateSprint($enableCreateSprint)
    {
        $this->enableCreateSprint = $enableCreateSprint;

        return $this;
    }

    /**
     * Get enableCreateSprint
     *
     * @return boolean 
     */
    public function getEnableCreateSprint()
    {
        return $this->enableCreateSprint;
    }

    /**
     * Set project
     *
     * @param \ZectranetBundle\Entity\Project $project
     * @return ProjectPermissions
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
     * @return ProjectPermissions
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
     * @param Project $project
     * @param User $user
     */
    public static function addPermission($em, $project, $user)
    {
        $permission = new ProjectPermissions();

        $permission->setProject($project);
        $permission->setUser($user);

        $em->persist($permission);
        $em->flush();
    }

    public static function savePermission($em, $project, $user, $enableCreateSprint)
    {
        $permission = $em->getRepository('ZectranetBundle:ProjectPermissions')->findOneBy(array(
            'projectid' => $project->getId(),
            'userid' => $user->getId()
        ));

        $permission->setEnableCreateSprint(($enableCreateSprint == null) ? false : true);

        $em->flush();
    }
}
