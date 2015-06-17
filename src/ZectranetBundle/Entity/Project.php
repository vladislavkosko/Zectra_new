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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true, options={"default" = null})
     */
    private $description;

    /**
     * @var int
     * @ORM\Column(name="type_id", type="integer")
     */
    private $typeID;

    /**
     * @var ProjectType
     * @ORM\ManyToOne(targetEntity="ProjectType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $type;

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
     * @ORM\OrderBy({"id" = "DESC"})
     * @var ArrayCollection
     */
    private $epicStories;

    /**
     * @ORM\OneToMany(targetEntity="Version", mappedBy="project", cascade={"remove"})
     * @ORM\OrderBy({"date" = "DESC"})
     * @var ArrayCollection
     */
    private $versions;

    /**
     * @ORM\OneToMany(targetEntity="ProjectPermissions", mappedBy="project", cascade={"remove"})
     * @var ArrayCollection
     */
    private $projectPermissions;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="projects", fetch="EXTRA_LAZY")
     * @var ArrayCollection
     */
    private $users;

    /**
     * @var Office
     * @ORM\Column(name="office_id", type="integer")
     */
    private $officeID;

    /**
     * @ORM\ManyToOne(targetEntity="Office", inversedBy="projects", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     * @var array
     */
    private $office;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="project", cascade={"remove"})
     * @var array
     */
    private $tasks;

    /**
     * @ORM\OneToMany(targetEntity="Sprint", mappedBy="project", cascade={"remove"})
     * @var ArrayCollection
     */
    private $sprints;

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
     * @var boolean
     * @ORM\Column(name="archived", type="boolean", options={"default" = false})
     */
    private $archived;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ProjectLog", mappedBy="project", cascade={"remove"})
     */
    private $logs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->sprints = new ArrayCollection();
        $this->postsProject = new ArrayCollection();
        $this->visible = false;
        $this->archived = false;
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
     * @param int $office_id
     * @return Project
     */
    public static function addEpicStory(EntityManager $em, $project_id, User $user, $data, $office_id) {
        $epicStory = new Project();
        $epicStory->setOwner($user);
        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);
        $epicStory->setParent($project);
        $office = $em->getRepository('ZectranetBundle:Office')->find($office_id);
        $epicStory->setOffice($office);
        $epicStory->setType($project->getType());
        $epicStory->setName($data->name);
        $em->persist($epicStory);
        $em->flush();

        return $epicStory;
    }

    /**
     * @param EntityManager $em
     * @param User $user
     * @param string $name
     * @param int $type_id
     * @param int $office_id
     * @return Project
     */
    public static function addNewProject(EntityManager $em, User $user, $name, $type_id, $office_id) {
        $project = new Project();
        $project->setOwner($user);
        $project->setName($name);
        $type = $em->getRepository('ZectranetBundle:ProjectType')->find($type_id);
        $project->setType($type);
        $office = $em->getRepository('ZectranetBundle:Office')->find($office_id);
        $project->setOffice($office);
        $project->addUser($user);
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
        $em->remove($project);
        $em->flush();
    }

    /**
     * @param EntityManager $em
     * @param $project_id
     * @return array
     */
    public static function getJsonProjectMembers(EntityManager $em, $project_id) {
        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);
        $jsonProjectUsers = array();
        /** @var User $user */
        foreach ($project->getUsers() as $user) {
            $jsonProjectUsers[] = $user->getInArray();
        }

        $requests = $em->getRepository('ZectranetBundle:Request')->findBy(array('projectid' => $project_id, 'typeid' => 2));
        if (count($requests) > 0)
        {
            foreach ($requests as $request){
                $usr = $request->getUser()->getInArray();
                $usr['request'] = 1;
                $jsonProjectUsers[] = $usr;
            }
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

        $requests = $em->getRepository('ZectranetBundle:Request')->findBy(array('projectid' => $project_id, 'typeid' => 2));
        if (count($requests) > 0)
            foreach ($requests as $request)
                $user_ids[] = $request->getUserid();

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
     * @param $project_id
     * @return array
     */
    public static function getJsonProjectOffices(EntityManager $em, $project_id)
    {
        $jsonProjectOffices = array();
        /*$project = $em->getRepository('ZectranetBundle:Project')->find($project_id);
        foreach ($project->getOffices() as $office) {
            $jsonProjectOffices[] = $office->getInArray();
        }

        $requests = $em->getRepository('ZectranetBundle:Request')->findBy(array('projectid' => $project_id, 'typeid' => 3));
        if (count($requests) > 0)
        {
            foreach ($requests as $request){
                $office = $request->getOffice()->getInArray();
                $office['request'] = 1;
                $jsonProjectOffices[] = $office;
            }
        }*/

        return $jsonProjectOffices;
    }

    /**
     * @param EntityManager $em
     * @param $project_id
     * @return array
     */
    public static function getJsonNotProjectOffices(EntityManager $em, $project_id)
    {
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);
        $office_ids = array();
        /** @var Office $office */
        /*foreach ($project->getOffices() as $office) {
            $office_ids[] = $office->getId();
        }*/

        $requests = $em->getRepository('ZectranetBundle:Request')->findBy(array('projectid' => $project_id, 'typeid' => 3));
        if (count($requests) > 0)
            foreach ($requests as $request)
                $office_ids[] = $request->getOfficeid();

        /*$notProjectOffices = array();
        if (count($office_ids) > 0) {
            $query = $qb->select('o')
                ->from('ZectranetBundle:Office', 'o')
                ->where($qb->expr()->notIn('o.id', $office_ids))
                ->andWhere('o.visible = 1')
                ->getQuery();
            $notProjectOffices = $query->getResult();
        } else {
            $notProjectOffices = $em->getRepository('ZectranetBundle:Office')->findBy(array('visible' => true));
        }*/

        $jsonNotProjectOffices = array();
        /** @var Office $office */
        /*foreach ($notProjectOffices as $office) {
            $jsonNotProjectOffices[] = $office->getInArray();
        }*/

        return $jsonNotProjectOffices;
    }

    /**
     * @param EntityManager $em
     * @param int $project_id
     * @param int $user_id
     * @param object $version
     * @return Version
     */
    public static function addNewProjectVersion(EntityManager $em, $project_id, $user_id, $version) {
        /** @var Project $project */
        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);
        /** @var User $user */
        $user = $em->getRepository('ZectranetBundle:User')->find($user_id);
        $newVersion = new Version();
        $newVersion->setName($version->name);
        $newVersion->setDescription($version->description);
        $newVersion->setDate(new \DateTime());
        $newVersion->setOwner($user);
        $newVersion->setProject($project);

        $em->persist($newVersion);
        $em->flush();

        return $newVersion;
    }

    /**
     * @param $projects
     * @param string $slug
     * @param null|int $limit
     * @return array
     */
    public static function searchProjects($projects, $slug, $limit = null)
    {
        $tasks = array();
        $taskPosts = array();
        $posts = array();
        $jsonProjects = array();

        /** @var Project $project */
        foreach ($projects as $project) {
            $matchesLength = preg_match('/' . $slug . '/mi', $project->getName(), $matches);
            if ($matchesLength > 0) {
                $jsonProjects[] = $project->getInArray();
            }

            $iterations = $limit;
            /** @var ProjectPost $post */
            foreach ($project->getPostsProject() as $post) {
                $matchesLength = preg_match('/' . $slug . '/mi', $post->getMessage(), $matches);
                if ($matchesLength > 0) {
                    $jsonPost = $post->getInArray();
                    $jsonPost['projectID'] = $post->getProjectid();
                    $posts[] = $jsonPost;
                    $iterations = ($iterations) ? $iterations - 1 : null;
                    if (!$iterations && $limit) break;
                }
            }

            $iterations = $limit;
            /** @var Task $task */
            foreach ($project->getTasks() as $task) {
                $matchesLength = preg_match('/' . $slug . '/mi', $task->getName(), $matches);
                $matchesLength += preg_match('/' . $slug . '/mi', $task->getDescription(), $matches);
                if ($matchesLength > 0) {
                    $jsonTask = $task->getInArray();
                    $parent = $task->getParent();
                    if ($parent) {
                        $subtasks = $parent->getSubtasks();
                        for ($i = 0; $i < count($subtasks); $i++) {
                            if ($subtasks[$i]->getId() == $task->getId()) {
                                $jsonTask['subindex'] = $i;
                            }
                        }
                    }
                    $tasks[] = $jsonTask;
                    $iterations = ($iterations) ? $iterations - 1 : null;
                }

                /** @var TaskPost $post */
                foreach ($task->getPosts() as $post) {
                    $matchesLength = preg_match('/' . $slug . '/mi', $post->getMessage(), $matches);
                    if ($matchesLength > 0) {
                        $jsonTaskPost = $post->getInArray();
                        $jsonTaskPost['projectID'] = $post->getTask()->getProjectid();
                        $taskPosts[] = $jsonTaskPost;
                        $iterations = ($iterations) ? $iterations - 1 : null;
                        if (!$iterations && $limit) break;
                    }
                }

                if (!$iterations && $limit) break;
            }
        }

        return array(
            'tasks' => $tasks,
            'taskPosts' => $taskPosts,
            'posts' => $posts,
            'projects' => $jsonProjects
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
     * Set officeID
     *
     * @param integer $officeID
     * @return Project
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
     * Add versions
     *
     * @param \ZectranetBundle\Entity\Version $versions
     * @return Project
     */
    public function addVersion(\ZectranetBundle\Entity\Version $versions)
    {
        $this->versions[] = $versions;

        return $this;
    }

    /**
     * Remove versions
     *
     * @param \ZectranetBundle\Entity\Version $versions
     */
    public function removeVersion(\ZectranetBundle\Entity\Version $versions)
    {
        $this->versions->removeElement($versions);
    }

    /**
     * Get versions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVersions()
    {
        return $this->versions;
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
     * @return Project
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
    public function addPostsProject(\ZectranetBundle\Entity\ProjectPost $postsProject)
    {
        $this->postsProject[] = $postsProject;

        return $this;
    }

    /**
     * Remove postsProject
     *
     * @param \ZectranetBundle\Entity\ProjectPost $postsProject
     */
    public function removePostsProject(\ZectranetBundle\Entity\ProjectPost $postsProject)
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
     * Set typeID
     *
     * @param integer $typeID
     * @return Project
     */
    public function setTypeID($typeID)
    {
        $this->typeID = $typeID;

        return $this;
    }

    /**
     * Get typeID
     *
     * @return integer
     */
    public function getTypeID()
    {
        return $this->typeID;
    }

    /**
     * Set type
     *
     * @param \ZectranetBundle\Entity\ProjectType $type
     * @return Project
     */
    public function setType(\ZectranetBundle\Entity\ProjectType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \ZectranetBundle\Entity\ProjectType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set archived
     *
     * @param boolean $archived
     * @return Project
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;

        return $this;
    }

    /**
     * Get archived
     *
     * @return boolean
     */
    public function getArchived()
    {
        return $this->archived;
    }

    /**
     * @param EntityManager $em
     * @param $user_id
     * @param $project_id
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public static function getNotProjectHomeOfficeMembers(EntityManager $em, $user_id, $project_id) {
        $user = $em->find('ZectranetBundle:User', $user_id);
        $project = $em->find('ZectranetBundle:Project', $project_id);
        $notProjectContacts = array();
        /** @var User $contact */
        foreach ($user->getContacts() as $contact) {
            if (!$project->getUsers()->contains($contact)) {
                $notProjectContacts[] = $contact->getInArray();
            }
        }
        return $notProjectContacts;
    }

    /**
     * @param EntityManager $em
     * @param $project_id
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public static function getNotProjectSiteMembers(EntityManager $em, $project_id) {
        $users = $em->getRepository('ZectranetBundle:User')->findAll();
        $project = $em->find('ZectranetBundle:Project', $project_id);
        $notProjectContacts = array();
        /** @var User $contact */
        foreach ($users as $contact) {
            if (!$project->getUsers()->contains($contact)) {
                $notProjectContacts[] = $contact->getInArray();
            }
        }
        return $notProjectContacts;
    }

    /**
     * @param EntityManager $em
     * @param $user_id
     * @param $project_id
     * @param $message
     * @param $initiator_id
     * @return Request
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public static function sendRequestToUser(EntityManager $em, $user_id, $project_id, $message, $initiator_id) {
        $project = $em->find('ZectranetBundle:Project', $project_id);
        $user = $em->find('ZectranetBundle:User', $user_id);
        $initiator = $em->find('ZectranetBundle:User', $initiator_id);
        $status = $em->find('ZectranetBundle:RequestStatus', 1);
        $type = RequestType::getDevelopmentMembershipRequest($em);

        // Check for old request
        $request = $em->getRepository('ZectranetBundle:Request')->findOneBy(array(
            'userid' => $user_id,
            'projectid' => $project_id,
            'typeid' => $type->getId(),
        ));
        // Delete old request if existing
        if ($request) {
            $em->remove($request);
        }
        // Create new request
        $request = new Request();
        $request->setType($type);
        $request->setUser($user);
        $request->setContact($initiator);
        $request->setMessage($message);
        $request->setProject($project);
        $request->setStatus($status);

        $em->persist($request);
        $em->flush();
        return $request;
    }

    /**
     * @param EntityManager $em
     * @param $request_id
     * @return null|Request
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public static function removeRequest(EntityManager $em, $request_id) {
        $request = $em->find('ZectranetBundle:Request', $request_id);
        if ($request) {
            $clone = clone $request;
            $em->remove($request);
            $em->flush();
            return $clone;
        } else {
            return null;
        }
    }

    /**
     * @param EntityManager $em
     * @param int $user_id
     * @param int $project_id
     */
    public static function addUserToProject(EntityManager $em, $user_id, $project_id) {
        $user = $em->find('ZectranetBundle:User', $user_id);
        $project = $em->find('ZectranetBundle:Project', $project_id);
        if (!$project->getUsers()->contains($user)) {
            $project->addUser($user);
            $em->persist($project);
            $em->flush();
        }
    }

    /**
     * @param EntityManager $em
     * @param $user_id
     * @param $project_id
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public static function removeUserFromProject(EntityManager $em, $user_id, $project_id) {
        $user = $em->find('ZectranetBundle:User', $user_id);
        $project = $em->find('ZectranetBundle:Project', $project_id);
        if ($project->getUsers()->contains($user)) {
            $project->removeUser($user);
            $em->persist($project);
            $em->flush();
        }
    }


    /**
     * Add logs
     *
     * @param \ZectranetBundle\Entity\ProjectLog $logs
     * @return Project
     */
    public function addLog(\ZectranetBundle\Entity\ProjectLog $logs)
    {
        $this->logs[] = $logs;

        return $this;
    }

    /**
     * Remove logs
     *
     * @param \ZectranetBundle\Entity\ProjectLog $logs
     */
    public function removeLog(\ZectranetBundle\Entity\ProjectLog $logs)
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
     * Add sprints
     *
     * @param \ZectranetBundle\Entity\Sprint $sprints
     * @return Project
     */
    public function addSprint(\ZectranetBundle\Entity\Sprint $sprints)
    {
        $this->sprints[] = $sprints;

        return $this;
    }

    /**
     * Remove sprints
     *
     * @param \ZectranetBundle\Entity\Sprint $sprints
     */
    public function removeSprint(\ZectranetBundle\Entity\Sprint $sprints)
    {
        $this->sprints->removeElement($sprints);
    }

    /**
     * Get sprints
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSprints()
    {
        return $this->sprints;
    }

    /**
     * Add projectPermissions
     *
     * @param \ZectranetBundle\Entity\ProjectPermissions $projectPermissions
     * @return Project
     */
    public function addProjectPermission(\ZectranetBundle\Entity\ProjectPermissions $projectPermissions)
    {
        $this->projectPermissions[] = $projectPermissions;

        return $this;
    }

    /**
     * Remove projectPermissions
     *
     * @param \ZectranetBundle\Entity\ProjectPermissions $projectPermissions
     */
    public function removeProjectPermission(\ZectranetBundle\Entity\ProjectPermissions $projectPermissions)
    {
        $this->projectPermissions->removeElement($projectPermissions);
    }

    /**
     * Get projectPermissions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjectPermissions()
    {
        return $this->projectPermissions;
    }
}
