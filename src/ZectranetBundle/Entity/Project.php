<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table(name="projects")
 * @ORM\Entity
 */
class Project
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="owner_id", type="integer")
     */
    private $ownerid;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ownedProjects")
     * @ORM\JoinColumn(name="owner_id")
     * @var User
     */
    private $owner;

    /**
     * @var integer
     * @ORM\Column(name="parent_id", type="integer", nullable=true, options={"default" = NULL})
     */
    private $parentid;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="epicStories")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * @var Project
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="parent", cascade={"remove"})
     * @var ArrayCollection
     */
    private $epicStories;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="projects", fetch="EXTRA_LAZY")
     * @var array
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="Office", inversedBy="projects", fetch="EXTRA_LAZY")
     * @var array
     */
    private $offices;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="project", cascade={"remove"})
     * @var array
     */
    private $tasks;

    /**
     * @ORM\OneToMany(targetEntity="ProjectPost", mappedBy="project", cascade={"remove"})
     * @ORM\OrderBy({"posted" = "DESC"})
     * @var array
     */
    private $postsProject;

    /**
     * @var bool $visible
     * @ORM\Column(name="visible", type="boolean", options={"default" = false})
     */
    private $visible;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->offices = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->postsProject = new ArrayCollection();
        $this->visible = false;
    }

    /**
     * @return array
     */
    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'parentid' => $this->getParentid(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'owner' => $this->getOwner()->getInArray(),
            'visible' => $this->getVisible(),
        );
    }

    /**
     * @param EntityManager $em
     * @param $project_id
     * @param User $user
     * @param $data
     * @return Project
     */
    public static function addEpicStory(EntityManager $em, $project_id, User $user, $data) {
        $epicStory = new Project();
        $epicStory->setOwner($user);
        $epicStory->setParent($em->getRepository('ZectranetBundle:Project')
            ->find($project_id));
        $epicStory->setName($data->name);
        $epicStory->setDescription($data->description);

        $em->persist($epicStory);
        $em->flush();

        return $epicStory;
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
     * @return Project
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
     * @return Project
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
     * Set ownerid
     *
     * @param integer $ownerid
     * @return Project
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
     * Set parentid
     *
     * @param integer $parentid
     * @return Project
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
     * Set owner
     *
     * @param \ZectranetBundle\Entity\User $owner
     * @return Project
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
     * Set parent
     *
     * @param \ZectranetBundle\Entity\Project $parent
     * @return Project
     */
    public function setParent(\ZectranetBundle\Entity\Project $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \ZectranetBundle\Entity\Project 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add users
     *
     * @param \ZectranetBundle\Entity\User $users
     * @return Project
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
     * @param $users
     */
    public function setUsers($users) {
        $this->users = $users;
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
     * Add offices
     *
     * @param \ZectranetBundle\Entity\Office $offices
     * @return Project
     */
    public function addOffice(\ZectranetBundle\Entity\Office $offices)
    {
        $this->offices[] = $offices;

        return $this;
    }

    /**
     * Remove offices
     *
     * @param \ZectranetBundle\Entity\Office $offices
     */
    public function removeOffice(\ZectranetBundle\Entity\Office $offices)
    {
        $this->offices->removeElement($offices);
    }

    /**
     * Get offices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOffices()
    {
        return $this->offices;
    }

    /**
     * Add tasks
     *
     * @param \ZectranetBundle\Entity\Task $tasks
     * @return Project
     */
    public function addTask(\ZectranetBundle\Entity\Task $tasks)
    {
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove tasks
     *
     * @param \ZectranetBundle\Entity\Task $tasks
     */
    public function removeTask(\ZectranetBundle\Entity\Task $tasks)
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
     * Add postsProject
     *
     * @param \ZectranetBundle\Entity\ProjectPost $postsProject
     * @return Project
     */
    public function addPostsProject(ProjectPost $postsProject)
    {
        $this->postsProject[] = $postsProject;

        return $this;
    }

    /**
     * Remove postsProject
     *
     * @param \ZectranetBundle\Entity\ProjectPost $postsProject
     */
    public function removePostsProject(ProjectPost $postsProject)
    {
        $this->postsProject->removeElement($postsProject);
    }

    /**
     * Get postsProject
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPostsProject()
    {
        return $this->postsProject;
    }

    /**
     * @param EntityManager $em
     * @param User $user
     * @param string $name
     * @param string $description
     * @return Project
     */
    public static function addNewProject(EntityManager $em, User $user, $name, $description) {
        $project = new Project();
        $project->setOwner($user);
        $project->setName($name);
        $project->setDescription($description);
        $em->persist($project);
        $em->flush();

        return $project;
    }

    /**
     * @param EntityManager $em
     * @param int $project_id
     */
    public static function deleteProject(EntityManager $em, $project_id) {
        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);

        /** @var Office $office */
        foreach ($project->getOffices() as $office) {
            $project->removeOffice($office);
            $em->remove($office);
        }

        /** @var ProjectPost $post */
        foreach ($project->getPostsProject() as $post) {
            $project->removePostsProject($post);
            $em->remove($post);
        }

        $em->remove($project);
        $em->flush();
    }

    /**
     * Remove epicStories
     *
     * @param \ZectranetBundle\Entity\Project $epicStories
     */
    public function removeEpicStory(\ZectranetBundle\Entity\Project $epicStories)
    {
        $this->epicStories->removeElement($epicStories);
    }

    /**
     * Get epicStories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEpicStories()
    {
        return $this->epicStories;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return Project
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @param EntityManager $em
     * @param int $project_id
     * @return array
     */
    public static function getJsonProjectMembers(EntityManager $em, $project_id) {
        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);
        $jsonProjectUsers = array();
        /** @var User $user */
        foreach ($project->getUsers() as $user) {
            $jsonProjectUsers[] = $user->getInArray();
        }
        return $jsonProjectUsers;
    }

    /**
     * @param EntityManager $em
     * @param $project_id
     * @return array
     */
    public static function getJsonNotProjectMembers(EntityManager $em, $project_id) {
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);
        $user_ids = array();
        /** @var User $user */
        foreach ($project->getUsers() as $user) {
            $user_ids[] = $user->getId();
        }

        $notProjectUsers = array();
        if (count($user_ids) > 0) {
            $query = $qb->select('u')
                ->from('ZectranetBundle:User', 'u')
                ->where($qb->expr()->notIn('u.id', $user_ids))
                ->getQuery();
            $notProjectUsers = $query->getResult();
        } else {
            $notProjectUsers = $em->getRepository('ZectranetBundle:User')->findAll();
        }

        $jsonNotProjectUsers = array();
        /** @var User $user */
        foreach ($notProjectUsers as $user) {
            if (count($user->getAssignedOffices()) == 0 && count($user->getOwnedOffices()) == 0) {
                $jsonNotProjectUsers[] = $user->getInArray();
            }
        }

        return $jsonNotProjectUsers;
    }

    /**
     * @param EntityManager $em
     * @param int $project_id
     * @return array
     */
    public static function getJsonProjectOffices(EntityManager $em, $project_id) {
        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);
        $jsonProjectOffices = array();
        /** @var Office $office */
        foreach ($project->getOffices() as $office) {
            $jsonProjectOffices[] = $office->getInArray();
        }
        return $jsonProjectOffices;
    }

    /**
     * @param EntityManager $em
     * @param $project_id
     * @return array
     */
    public static function getJsonNotProjectOffices(EntityManager $em, $project_id) {
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);
        $office_ids = array();
        /** @var Office $office */
        foreach ($project->getOffices() as $office) {
            $office_ids[] = $office->getId();
        }

        $notProjectOffices = array();
        if (count($office_ids) > 0) {
            $query = $qb->select('o')
                ->from('ZectranetBundle:Office', 'o')
                ->where($qb->expr()->notIn('o.id', $office_ids))
                ->andWhere('o.visible = 1')
                ->getQuery();
            $notProjectOffices = $query->getResult();
        } else {
            $notProjectOffices = $em->getRepository('ZectranetBundle:Office')->findBy(array('visible' => true));
        }

        $jsonNotProjectOffices = array();
        /** @var Office $office */
        foreach ($notProjectOffices as $office) {
            $jsonNotProjectOffices[] = $office->getInArray();
        }

        return $jsonNotProjectOffices;
    }

    /**
     * @param EntityManager $em
     * @param array $office_ids
     * @param int $project_id
     */
    public static function addOfficesToProject(EntityManager $em, $office_ids, $project_id) {
        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);
        $offices = $em->getRepository('ZectranetBundle:Office')->findBy(array('id' => $office_ids));
        /** @var ArrayCollection $projectOffices */
        $projectOffices = $project->getOffices();
        /** @var Office $office */
        foreach ($offices as $office) {
            if (!$projectOffices->contains($office)) {
                $project->addOffice($office);
            }
        }

        $em->persist($project);
        $em->flush();
    }

    /**
     * @param EntityManager $em
     * @param array $office_ids
     * @param int $project_id
     */
    public static function removeOfficesFromProject(EntityManager $em, $office_ids, $project_id) {
        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);
        $offices = $em->getRepository('ZectranetBundle:Office')->findBy(array('id' => $office_ids));
        /** @var ArrayCollection $projectOffices */
        $projectOffices = $project->getOffices();
        /** @var Office $office */
        foreach ($offices as $office) {
            if ($projectOffices->contains($office)) {
                $project->removeOffice($office);
            }
        }

        $em->persist($project);
        $em->flush();
    }
}
