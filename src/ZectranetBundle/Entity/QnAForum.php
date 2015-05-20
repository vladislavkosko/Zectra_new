<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * QnAForum
 *
 * @ORM\Table(name="QnA_forums")
 * @ORM\Entity
 */
class QnAForum
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     * @ORM\Column(name="onwer_id", type="integer")
     */
    private $ownerID;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="onwer_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="connectedQnAForums", fetch="EXTRA_LAZY")
     * @var ArrayCollection
     */
    private $users;

    /**
     * @var int
     * @ORM\Column(name="office_id", type="integer")
     */
    private $officeID;

    /**
     * @var Office
     * @ORM\ManyToOne(targetEntity="Office", inversedBy="QnAForums")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QnAThread", mappedBy="forum", cascade={"remove"})
     */
    private $threads;

    /**
     * @var boolean
     * @ORM\Column(name="shared", type="boolean", options={"default" = false})
     */
    private $shared;

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QnALog", mappedBy="project", cascade={"remove"})
     */
    private $logs;

    /**
     * @var boolean
     * @ORM\Column(name="archived", type="boolean", options={"default" = false})
     */
    private $archived;

    /**
     * @return array
     */
    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'officeID' => $this->getOfficeID(),
            'ownerID' => $this->getOwnerID(),
        );
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->users = new ArrayCollection();
        $this->created = new \DateTime();
        $this->shared = false;
        $this->logs = new ArrayCollection();
        $this->archived = false;
    }

    /**
     * @param EntityManager $em
     * @param int $user_id
     * @param int $project_id
     * @param string $message
     * @param int $initiator_id
     * @return Request
     */
    public static function sendRequestToUser(EntityManager $em, $user_id, $project_id, $message, $initiator_id) {
        $project = $em->find('ZectranetBundle:QnAForum', $project_id);
        $user = $em->find('ZectranetBundle:User', $user_id);
        $initiator = $em->find('ZectranetBundle:User', $initiator_id);
        $status = $em->find('ZectranetBundle:RequestStatus', 1);
        $type = RequestType::getQnAMembershipRequest($em);

        // Check for old request
        $request = $em->getRepository('ZectranetBundle:Request')->findOneBy(array(
            'userid' => $user_id,
            'QnAForumID' => $project_id,
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
        $request->setQnAForum($project);
        $request->setStatus($status);

        $em->persist($request);
        $em->flush();
        return $request;
    }

    /**
     * @param EntityManager $em
     * @param $request_id
     * @return mixed
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
     * @return array
     */
    public static function getNotProjectHomeOfficeMembers(EntityManager $em, $user_id, $project_id) {
        $user = $em->find('ZectranetBundle:User', $user_id);
        $project = $em->find('ZectranetBundle:QnAForum', $project_id);
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
     * @param int $project_id
     * @return array
     */
    public static function getNotProjectSiteMembers(EntityManager $em, $project_id) {
        $users = $em->getRepository('ZectranetBundle:User')->findAll();
        $project = $em->find('ZectranetBundle:QnAForum', $project_id);
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
     * @param int $user_id
     * @param int $project_id
     */
    public static function addUserToProject(EntityManager $em, $user_id, $project_id) {
        $user = $em->find('ZectranetBundle:User', $user_id);
        $project = $em->find('ZectranetBundle:QnAForum', $project_id);
        if (!$project->getUsers()->contains($user)) {
            $project->addUser($user);
            $em->persist($project);
            $em->flush();
        }
    }

    /**
     * @param EntityManager $em
     * @param int $user_id
     * @param int $project_id
     */
    public static function removeUserFromProject(EntityManager $em, $user_id, $project_id) {
        $user = $em->find('ZectranetBundle:User', $user_id);
        $project = $em->find('ZectranetBundle:QnAForum', $project_id);
        if ($project->getUsers()->contains($user)) {
            $project->removeUser($user);
            $em->persist($project);
            $em->flush();
        }
    }

    /**
     * @param $forums
     * @param string $slug
     * @param null|int $limit
     * @return array
     */
    public static function searchQnAForums($forums, $slug, $limit = null) {
        $threads = array();
        $posts = array();
        $jsonForums = array();
        $iterations = $limit;
        /** @var QnAForum $forum */
        foreach ($forums as $forum) {
            $matchesLength = preg_match('/' . $slug . '/mi', $forum->getName(), $matches);
            if ($matchesLength > 0) {
                $jsonForums[] = $forum->getInArray();
            }
            /** @var QnAThread $thread */
            foreach ($forum->getThreads() as $thread) {
                $matchesLength = preg_match('/' . $slug . '/mi', $thread->getTitle(), $matches);
                if ($matchesLength > 0) {
                    $threads[] = $thread->getInArray();
                    $iterations = ($iterations) ? $iterations - 1 : null;
                }
                /** @var QnAPost $post */
                foreach ($thread->getPosts() as $post) {
                    $matchesLength = preg_match('/' . $slug . '/mi', $post->getMessage(), $matches);
                    if ($matchesLength > 0) {
                        $jsonPost = $post->getInArray();
                        $jsonPost['forumID'] = $post->getThread()->getForumID();
                        $posts[] = $jsonPost;
                        $iterations = ($iterations) ? $iterations - 1 : null;
                    }
                    if (!$iterations && $limit) break;
                }
                if (!$iterations && $limit) break;
            }
        }
        return array(
            'forums' => $jsonForums,
            'threads' => $threads,
            'posts' => $posts,
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
     * @return QnAForum
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
     * Set ownerID
     *
     * @param integer $ownerID
     * @return QnAForum
     */
    public function setOwnerID($ownerID)
    {
        $this->ownerID = $ownerID;

        return $this;
    }

    /**
     * Get ownerID
     *
     * @return integer 
     */
    public function getOwnerID()
    {
        return $this->ownerID;
    }

    /**
     * Set officeID
     *
     * @param integer $officeID
     * @return QnAForum
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
     * Set shared
     *
     * @param boolean $shared
     * @return QnAForum
     */
    public function setShared($shared)
    {
        $this->shared = $shared;

        return $this;
    }

    /**
     * Get shared
     *
     * @return boolean 
     */
    public function getShared()
    {
        return $this->shared;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return QnAForum
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set owner
     *
     * @param \ZectranetBundle\Entity\User $owner
     * @return QnAForum
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
     * @return QnAForum
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
     * @return QnAForum
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
     * Add threads
     *
     * @param \ZectranetBundle\Entity\QnAThread $threads
     * @return QnAForum
     */
    public function addThread(\ZectranetBundle\Entity\QnAThread $threads)
    {
        $this->threads[] = $threads;

        return $this;
    }

    /**
     * Remove threads
     *
     * @param \ZectranetBundle\Entity\QnAThread $threads
     */
    public function removeThread(\ZectranetBundle\Entity\QnAThread $threads)
    {
        $this->threads->removeElement($threads);
    }

    /**
     * Get threads
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getThreads()
    {
        return $this->threads;
    }

    /**
     * @param EntityManager $em
     * @param User $user
     * @param $office_id
     * @param $name
     * @return HFForum
     */
    public static function addNewQnAForum($em, $user, $office_id, $name)
    {
        $office = $em->getRepository('ZectranetBundle:Office')->find($office_id);

        $project = new QnAForum();
        $project->setOffice($office);
        $project->setOwner($user);
        $project->setName($name);
        $project->addUser($user);

        $em->persist($project);
        $em->flush();

        return $project;
    }

    /**
     * Add logs
     *
     * @param \ZectranetBundle\Entity\QnALog $logs
     * @return QnAForum
     */
    public function addLog(\ZectranetBundle\Entity\QnALog $logs)
    {
        $this->logs[] = $logs;

        return $this;
    }

    /**
     * Remove logs
     *
     * @param \ZectranetBundle\Entity\QnALog $logs
     */
    public function removeLog(\ZectranetBundle\Entity\QnALog $logs)
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
     * Set archived
     *
     * @param boolean $archived
     * @return QnAForum
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
}
