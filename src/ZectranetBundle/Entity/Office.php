<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Office
 *
 * @ORM\Table(name="offices")
 * @ORM\Entity
 */
class Office
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
     * @var integer
     *
     * @ORM\Column(name="owner_id", type="integer", nullable=true)
     */
    private $ownerid;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ownedOffices")
     * @ORM\JoinColumn(name="owner_id")
     * @var User
     */
    private $owner;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="assignedOffices", fetch="EXTRA_LAZY")
     * @var ArrayCollection
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="office", fetch="EXTRA_LAZY")
     * @var ArrayCollection
     */
    private $projects;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="HFForum", mappedBy="office")
     */
    private $headerForums;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QnAForum", mappedBy="office")
     */
    private $QnAForums;

    /**
     * @ORM\OneToMany(targetEntity="OfficePost", mappedBy="office", cascade={"remove"})
     * @ORM\OrderBy({"posted" = "DESC"})
     * @var ArrayCollection
     */
    private $postsOffice;

    /**
     * @ORM\OneToMany(targetEntity="OfficeRole", mappedBy="office", cascade={"remove"})
     * @var ArrayCollection
     */
    private $officeUserRoles;

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
     * @var OfficeProfile
     * @ORM\OneToOne(targetEntity="OfficeProfile", mappedBy="office")
     */
    private $profile;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="OfficeLog", mappedBy="office")
     */
    private $logs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->visible = false;
        $this->archived = false;
        $this->logs = new ArrayCollection();
    }

    /**
     * @param EntityManager $em
     * @param int $office_id
     * @return array
     */
    public static function getOfficeArchive(EntityManager $em, $office_id) {
        $archives = array(
            'projects' => EntityOperations::arrayToJsonArray(
                $em->getRepository('ZectranetBundle:Project')->findBy(array('archived' => true, 'officeID' => $office_id))
            ),
            'hfForums' => EntityOperations::arrayToJsonArray(
                $em->getRepository('ZectranetBundle:HFForum')->findBy(array('archived' => true, 'officeID' => $office_id))
            ),
            'QnAForums' => EntityOperations::arrayToJsonArray(
                $em->getRepository('ZectranetBundle:QnAForum')->findBy(array('archived' => true, 'officeID' => $office_id))
            ),
        );

        return $archives;
    }

    /**
     * @param EntityManager $em
     * @param int $project_id
     * @param int $project_type
     * @return null|Office
     */
    public static function addToArchive(EntityManager $em, $project_id, $project_type) {
        $project = null;
        switch ($project_type) {
            case 1: $project = $em->find('ZectranetBundle:QnAForum', $project_id); break;
            case 2: $project = $em->find('ZectranetBundle:HFForum', $project_id); break;
            case 3: /*$project = $em->find('ZectranetBundle:QnAForum', $project_id);*/ break;
            case 4: $project = $em->find('ZectranetBundle:Project', $project_id); break;
        }
        if ($project) {
            $project->setArchived(true);
            $em->persist($project);
            $em->flush();
            return $project->getOfficeID();
        } else {
            return null;
        }
    }

    /**
     * @param EntityManager $em
     * @param int $project_id
     * @param int $project_type
     */
    public static function restoreFromArchive(EntityManager $em, $project_id, $project_type) {
        $project = null;
        switch ($project_type) {
            case 1: $project = $em->find('ZectranetBundle:QnAForum', $project_id); break;
            case 2: $project = $em->find('ZectranetBundle:HFForum', $project_id); break;
            case 3: /*$project = $em->find('ZectranetBundle:QnAForum', $project_id);*/ break;
            case 4: $project = $em->find('ZectranetBundle:Project', $project_id); break;
        }
        if ($project) {
            $project->setArchived(false);
            $em->persist($project);
            $em->flush();
        }
    }

    /**
     * @param EntityManager $em
     * @param $project_id
     * @param $project_type
     */
    public static function deleteFromArchive(EntityManager $em, $project_id, $project_type) {
        $project = null;
        switch ($project_type) {
            case 1: $project = $em->find('ZectranetBundle:QnAForum', $project_id); break;
            case 2: $project = $em->find('ZectranetBundle:HFForum', $project_id); break;
            case 3: /*$project = $em->find('ZectranetBundle:QnAForum', $project_id);*/ break;
            case 4: $project = $em->find('ZectranetBundle:Project', $project_id); break;
        }
        if ($project) {
            $em->remove($project);
            $em->flush();
        }
    }

    /**
     * @param EntityManager $em
     * @param int $office_id
     * @param string $slug
     * @param null|int $limit
     * @return array
     */
    public static function searchHomeOffice(EntityManager $em, $office_id, $slug, $limit = null) {
        $office = $em->find('ZectranetBundle:Office', $office_id);
        /** @var User $user */
        $user = $office->getOwner();
        $conversations = Conversation::getConversationByUser($em, $user->getId());
        $jsonMessages = array();

        $iterations = $limit;
        /** @var Conversation $conv */
        foreach ($conversations as $conv) {
            /** @var ConversationMessage $message */
            foreach ($conv->getMessages() as $message) {
                $matchesLength = preg_match('/' . $slug . '/mi', $message->getMessage(), $matches);
                if ($matchesLength > 0) {
                    $jsonMessage = $message->getInArray();
                    $jsonMessage['contact_id'] = ($message->getConversation()->getUser1ID() == $user->getId())
                        ? $message->getConversation()->getUser2ID()
                        : $message->getConversation()->getUser1ID();
                    $jsonMessages[] = $jsonMessage;
                    $iterations = ($iterations) ? $iterations - 1 : null;
                }
                if (!$iterations && $limit) break;
            }
        }
        return $jsonMessages;
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
     * @return Office
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
     * @return Office
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
     * @return Office
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
     * Set owner
     *
     * @param \ZectranetBundle\Entity\User $owner
     * @return Office
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
     * @return Office
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
     * Add projects
     *
     * @param \ZectranetBundle\Entity\Project $projects
     * @return Office
     */
    public function addProject(\ZectranetBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;

        return $this;
    }

    /**
     * Remove projects
     *
     * @param \ZectranetBundle\Entity\Project $projects
     */
    public function removeProject(\ZectranetBundle\Entity\Project $projects)
    {
        $this->projects->removeElement($projects);
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Add postsOffice
     *
     * @param \ZectranetBundle\Entity\OfficePost $postsOffice
     * @return Office
     */
    public function addPostsOffice(\ZectranetBundle\Entity\OfficePost $postsOffice)
    {
        $this->postsOffice[] = $postsOffice;

        return $this;
    }

    /**
     * Remove postsOffice
     *
     * @param \ZectranetBundle\Entity\OfficePost $postsOffice
     */
    public function removePostsOffice(\ZectranetBundle\Entity\OfficePost $postsOffice)
    {
        $this->postsOffice->removeElement($postsOffice);
    }

    /**
     * Get postsOffice
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPostsOffice()
    {
        return $this->postsOffice;
    }

    /**
     * Add officeUserRoles
     *
     * @param \ZectranetBundle\Entity\OfficeRole $officeUserRoles
     * @return Office
     */
    public function addOfficeUserRole(\ZectranetBundle\Entity\OfficeRole $officeUserRoles)
    {
        $this->officeUserRoles[] = $officeUserRoles;

        return $this;
    }

    /**
     * Remove officeUserRoles
     *
     * @param \ZectranetBundle\Entity\OfficeRole $officeUserRoles
     */
    public function removeOfficeUserRole(\ZectranetBundle\Entity\OfficeRole $officeUserRoles)
    {
        $this->officeUserRoles->removeElement($officeUserRoles);
    }

    /**
     * Get officeUserRoles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOfficeUserRoles()
    {
        return $this->officeUserRoles;
    }

    /**
     * @param EntityManager $em
     * @param User $user
     * @param $name
     * @param $description
     * @return Office
     */
    public static function addNewOffice(EntityManager $em, User $user, $name, $description) {
        $office = new Office();
        $office->setOwner($user);
        $office->setName($name);
        $office->setDescription($description);
        $em->persist($office);
        $em->flush();

        return $office;
    }

    /**
     * @param EntityManager $em
     * @param int $office_id
     */
    public static function deleteOffice(EntityManager $em, $office_id) {
        /** @var Office $office */
        $office = $em->getRepository('ZectranetBundle:Office')->find($office_id);

        $em->remove($office);
        $em->flush();
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return Office
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
     * @param $users
     */
    public function setUsers($users) {
        $this->users = $users;
    }

    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'description' => $this->getDescription(),
            'name' => $this->getName(),
            'owner' => $this->getOwner()->getInArray(),
            'visible' => $this->getVisible(),
        );
    }

    /**
     * @param EntityManager $em
     * @param int $office_id
     * @return array
     */
    public static function getJsonOfficeMembers(EntityManager $em, $office_id) {
        $office = $em->getRepository('ZectranetBundle:Office')->find($office_id);
        $jsonOfficeUsers = array();
        /** @var User $user */
        foreach ($office->getUsers() as $user) {
            $jsonOfficeUsers[] = $user->getInArray();
        }

        $requests = $em->getRepository('ZectranetBundle:Request')->findBy(array('officeid' => $office_id, 'typeid' => 1));
        if (count($requests) > 0)
        {
            foreach ($requests as $request){
                $usr = $request->getUser()->getInArray();
                $usr['request'] = 1;
                $jsonOfficeUsers[] = $usr;
            }
        }

        return $jsonOfficeUsers;
    }

    /**
     * @param EntityManager $em
     * @param $office_id
     * @return array
     */
    public static function getJsonNotOfficeMembers(EntityManager $em, $office_id) {
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $office = $em->getRepository('ZectranetBundle:Office')->find($office_id);
        $user_ids = array();
        /** @var User $user */
        foreach ($office->getUsers() as $user) {
            $user_ids[] = $user->getId();
        }

        $requests = $em->getRepository('ZectranetBundle:Request')->findBy(array('officeid' => $office_id, 'typeid' => 1));
        if (count($requests) > 0)
            foreach ($requests as $request)
                $user_ids[] = $request->getUserid();

        $notOfficeUsers = array();
        if (count($user_ids) > 0) {
            $query = $qb->select('u')
                ->from('ZectranetBundle:User', 'u')
                ->where($qb->expr()->notIn('u.id', $user_ids))
                ->getQuery();
            $notOfficeUsers = $query->getResult();
        } else {
            $notOfficeUsers = $em->getRepository('ZectranetBundle:User')->findAll();
        }

        $jsonNotOfficeUsers = array();
        /** @var User $user */
        foreach ($notOfficeUsers as $user) {
            if (!$user->getAssignedOffices()->contains($office)
                && !$user->getOwnedOffices()->contains($office)) {
                $jsonNotOfficeUsers[] = $user->getInArray();
            }

        }

        return $jsonNotOfficeUsers;
    }

    /**
     * Add headerForums
     *
     * @param \ZectranetBundle\Entity\HFForum $headerForums
     * @return Office
     */
    public function addHeaderForum(\ZectranetBundle\Entity\HFForum $headerForums)
    {
        $this->headerForums[] = $headerForums;

        return $this;
    }

    /**
     * Remove headerForums
     *
     * @param \ZectranetBundle\Entity\HFForum $headerForums
     */
    public function removeHeaderForum(\ZectranetBundle\Entity\HFForum $headerForums)
    {
        $this->headerForums->removeElement($headerForums);
    }

    /**
     * Get headerForums
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHeaderForums()
    {
        return $this->headerForums;
    }

    /**
     * Add QnAForums
     *
     * @param \ZectranetBundle\Entity\QnAForum $qnAForums
     * @return Office
     */
    public function addQnAForum(\ZectranetBundle\Entity\QnAForum $qnAForums)
    {
        $this->QnAForums[] = $qnAForums;

        return $this;
    }

    /**
     * Remove QnAForums
     *
     * @param \ZectranetBundle\Entity\QnAForum $qnAForums
     */
    public function removeQnAForum(\ZectranetBundle\Entity\QnAForum $qnAForums)
    {
        $this->QnAForums->removeElement($qnAForums);
    }

    /**
     * Get QnAForums
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQnAForums()
    {
        return $this->QnAForums;
    }

    /**
     * Set archived
     *
     * @param boolean $archived
     * @return Office
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
     * Set profile
     *
     * @param \ZectranetBundle\Entity\OfficeProfile $profile
     * @return Office
     */
    public function setProfile(\ZectranetBundle\Entity\OfficeProfile $profile = null)
    {
        $this->profile = $profile;
    
        return $this;
    }

    /**
     * Get profile
     *
     * @return \ZectranetBundle\Entity\OfficeProfile 
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Add logs
     *
     * @param \ZectranetBundle\Entity\OfficeLog $logs
     * @return Office
     */
    public function addLog(\ZectranetBundle\Entity\OfficeLog $logs)
    {
        $this->logs[] = $logs;

        return $this;
    }

    /**
     * Remove logs
     *
     * @param \ZectranetBundle\Entity\OfficeLog $logs
     */
    public function removeLog(\ZectranetBundle\Entity\OfficeLog $logs)
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
}
