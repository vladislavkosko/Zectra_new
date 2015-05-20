<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * HFForum
 *
 * @ORM\Table(name="header_forums")
 * @ORM\Entity
 */
class HFForum
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @ORM\ManyToMany(targetEntity="User", inversedBy="connectedHFForums", fetch="EXTRA_LAZY")
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
     * @ORM\ManyToOne(targetEntity="Office", inversedBy="headerForums")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="HFHeader", mappedBy="forum", cascade={"remove"})
     */
    private $headers;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

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
     * @ORM\OneToMany(targetEntity="HFLog", mappedBy="project", cascade={"remove"})
     * @ORM\OrderBy({"date" = "DESC"})
     */
    private $logs;

    /**
     * @var boolean
     * @ORM\Column(name="archived", type="boolean", options={"default" = false})
     */
    private $archived;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Request", mappedBy="HFForum", cascade={"remove"})
     */
    private $requests;

    /**
     * Constructor
     */
    public function __construct() {
        $this->created = new \DateTime();
        $this->shared = false;
        $this->users = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->archived = false;
        $this->requests = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'ownerID' => $this->getOwnerID(),
            'shared' => $this->getShared(),
            'headers' => EntityOperations::arrayToJsonArray($this->getHeaders()),
        );
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
        $project = $em->find('ZectranetBundle:HFForum', $project_id);
        $user = $em->find('ZectranetBundle:User', $user_id);
        $initiator = $em->find('ZectranetBundle:User', $initiator_id);
        $status = $em->find('ZectranetBundle:RequestStatus', 1);
        $type = RequestType::getProjectMembershipRequest($em);

        // Check for old request
        $request = $em->getRepository('ZectranetBundle:Request')->findOneBy(array(
            'userid' => $user_id,
            'HFForumID' => $project_id,
            'typeid' => $type->getId(),
        ));
        // Delete existing request
        if ($request) {
            $em->remove($request);
        }

        // Create new request
        $request = new Request();
        $request->setType($type);
        $request->setUser($user);
        $request->setContact($initiator);
        $request->setMessage($message);
        $request->setHFForum($project);
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
        $project = $em->find('ZectranetBundle:HFForum', $project_id);
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
        $project = $em->find('ZectranetBundle:HFForum', $project_id);
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
        $project = $em->find('ZectranetBundle:HFForum', $project_id);
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
     * @return boolean
     */
    public static function removeUserFromProject(EntityManager $em, $user_id, $project_id) {
        $user = $em->find('ZectranetBundle:User', $user_id);
        $project = $em->find('ZectranetBundle:HFForum', $project_id);
        if ($project->getUsers()->contains($user)) {
            $project->removeUser($user);
            $em->persist($project);
            $em->flush();
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param EntityManager $em
     * @param int $user_id
     * @param int $office_id
     * @param array $params
     * @return HFForum
     */
    public static function addNewHeaderForum(EntityManager $em, $user_id, $office_id, $params) {
        $user = $em->getRepository('ZectranetBundle:User')->find($user_id);
        $office = $em->getRepository('ZectranetBundle:Office')->find($office_id);
        $project = new HFForum();
        $project->setOffice($office);
        $project->setOwner($user);
        $project->setName($params['name']);
        $project->addUser($user);
        $em->persist($project);
        $em->flush();

        return $project;
    }

    /**
     * @param $forums
     * @param string $slug
     * @param null|int $limit
     * @return array
     */
    public static function searchHFForums($forums, $slug, $limit = null) {
        $threads = array();
        $posts = array();
        $jsonForums = array();
        $iterations = $limit;
        /** @var HFForum $forum */
        foreach ($forums as $forum) {
            $matchesLength = preg_match('/' . $slug . '/mi', $forum->getName(), $matches);
            if ($matchesLength > 0) {
                $jsonForums[] = $forum->getInArray();
            }
            /** @var HFHeader $header */
            foreach ($forum->getHeaders() as $header) {
                /** @var HFSubHeader $sub */
                foreach ($header->getSubHeaders() as $sub) {
                    /** @var HFThread $thread */
                    foreach ($sub->getThreads() as $thread) {
                        $matchesLength = preg_match('/' . $slug . '/mi', $thread->getTitle(), $matches);
                        if ($matchesLength > 0) {
                            $jsonThread = $thread->getInArray();
                            $jsonThread['HFForumID'] = $thread->getSubHeader()->getHeader()->getForumID();
                            $threads[] = $jsonThread;
                            $iterations = ($iterations) ? $iterations - 1 : null;
                        }
                        /** @var HFThreadPost $post */
                        foreach ($thread->getPosts() as $post) {
                            $matchesLength = preg_match('/' . $slug . '/mi', $post->getMessage(), $matches);
                            if ($matchesLength > 0) {
                                $jsonPost = $post->getInArray();
                                $jsonPost['subHeaderID'] = $post->getThread()->getSubHeaderID();
                                $jsonPost['HFForumID'] = $post->getThread()->getSubHeader()->getHeader()->getForumID();
                                $posts[] = $jsonPost;
                                $iterations = ($iterations) ? $iterations - 1 : null;
                            }
                            if (!$iterations && $limit) break;
                        }
                        if (!$iterations && $limit) break;
                    }
                }
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
     * Set ownerID
     *
     * @param integer $ownerID
     * @return HFForum
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
     * Set shared
     *
     * @param boolean $shared
     * @return HFForum
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
     * Set owner
     *
     * @param \ZectranetBundle\Entity\User $owner
     * @return HFForum
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
     * Set name
     *
     * @param string $name
     * @return HFForum
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
     * Set created
     *
     * @param \DateTime $created
     * @return HFForum
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
     * Add headers
     *
     * @param \ZectranetBundle\Entity\HFHeader $headers
     * @return HFForum
     */
    public function addHeader(\ZectranetBundle\Entity\HFHeader $headers)
    {
        $this->headers[] = $headers;

        return $this;
    }

    /**
     * Remove headers
     *
     * @param \ZectranetBundle\Entity\HFHeader $headers
     */
    public function removeHeader(\ZectranetBundle\Entity\HFHeader $headers)
    {
        $this->headers->removeElement($headers);
    }

    /**
     * Get headers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set officeID
     *
     * @param integer $officeID
     * @return HFForum
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
     * Set office
     *
     * @param \ZectranetBundle\Entity\Office $office
     * @return HFForum
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
     * Add users
     *
     * @param \ZectranetBundle\Entity\User $users
     * @return HFForum
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
     * Add logs
     *
     * @param \ZectranetBundle\Entity\HFLog $logs
     * @return HFForum
     */
    public function addLog(\ZectranetBundle\Entity\HFLog $logs)
    {
        $this->logs[] = $logs;

        return $this;
    }

    /**
     * Remove logs
     *
     * @param \ZectranetBundle\Entity\HFLog $logs
     */
    public function removeLog(\ZectranetBundle\Entity\HFLog $logs)
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
     * @return HFForum
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
     * Add requests
     *
     * @param \ZectranetBundle\Entity\Request $requests
     * @return HFForum
     */
    public function addRequest(\ZectranetBundle\Entity\Request $requests)
    {
        $this->requests[] = $requests;

        return $this;
    }

    /**
     * Get requests
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRequests()
    {
        return $this->requests;
    }
}
