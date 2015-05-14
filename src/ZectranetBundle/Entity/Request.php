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
     * @ORM\Column(name="project_id", type="integer", nullable=true, options={"default" = null})
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
     * @ORM\Column(name="hf_forum_id", type="integer", nullable=true, options={"default" = null})
     */
    private $HFForumID;

    /**
     * @var HFForum
     * @ORM\ManyToOne(targetEntity="HFForum", inversedBy="requests")
     * @ORM\JoinColumn(name="hf_forum_id", referencedColumnName="id")
     */
    private $HFForum;

    /**
     * @var integer
     *
     * @ORM\Column(name="QnA_forum_id", type="integer", nullable=true, options={"default" = null})
     */
    private $QnAForumID;

    /**
     * @var QnAForum
     * @ORM\ManyToOne(targetEntity="QnAForum")
     * @ORM\JoinColumn(name="QnA_forum_id", referencedColumnName="id")
     */
    private $QnAForum;

    /**
     * @var integer
     *
     * @ORM\Column(name="office_id", type="integer", nullable=true, options={"default" = null})
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
     * @ORM\Column(name="task_id", type="integer", nullable=true, options={"default" = null})
     */
    private $taskid;

    /**
     * @var Task
     * @ORM\ManyToOne(targetEntity="Task")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id")
     */
    private $task;

    /**
     * @var int
     * @ORM\Column(name="contact_id", type="integer", nullable=true, options={"default" = null})
     */
    private $contactID;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     */
    private $contact;

    /**
     * @var string
     * @ORM\Column(name="message", type="string", length=2000, nullable=true, options={"default" = null})
     */
    private $message;

    /**
     * @var int
     * @ORM\Column(name="status_id", type="integer")
     */
    private $statusID;

    /**
     * @var RequestStatus
     * @ORM\ManyToOne(targetEntity="RequestStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="datetime", nullable=true, options={"default" = null})
     */
    private $date;

    /**
     * Constructor
     */
    public function __construct() {
        $this->officeid = null;
        $this->projectid = null;
        $this->contactID = null;
        $this->message = null;
        $this->date = new \DateTime();
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
    public function setOffice(Office $office = null)
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
    public function setTask(Task $task = null)
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

    public function getInArray()
    {
        return array(
            'id' => $this->getId(),
            'type' => $this->getType()->getInArray(),
            'status' => $this->getStatus()->getInArray(),
            'date' => ($this->getDate())
                ? $this->getDate()->format('Y-m-d H:i:s')
                : null,
            'user' =>$this->getUser()->getInArray(),
            'project' => ($this->getProject())
                ? $this->getProject()->getInArray()
                : null,
            'message' => $this->getMessage(),
            'contact' => ($this->getUser())
                ? $this->getContact()->getInArray()
                : null,
            'hfForum' => ($this->getHFForum())
                ? $this->getHFForum()->getInArray()
                : null,
            'QnAForum' => ($this->getQnAForum())
                ? $this->getQnAForum()->getInArray()
                : null,

        );
    }

    /**
     * @param EntityManager $em
     * @param int $userID
     * @param int $typeID
     * @param null|string $message
     * @param int $sourceID
     * @return Request
     */
    public static function addNewRequest(EntityManager $em, $userID, $typeID, $message = null, $sourceID)
    {
        /** @var Request $new_request */
        $new_request = new Request();
        $user = $em->find('ZectranetBundle:User', $userID);
        $type = $em->find('ZectranetBundle:RequestType', $typeID);
        $status = $em->find('ZectranetBundle:RequestStatus', 1);

        $new_request->setType($type);
        $new_request->setUser($user);
        $new_request->setStatus($status);
        $new_request->setMessage($message);

        switch ($type->getId()) {
            case 1: break;
            case 2: break;
            case 3: break;
            case 4: break;
            case 5:
                $contact = $em->find('ZectranetBundle:User', $sourceID);
                $new_request->setContact($contact);
                break;
            case 6: break;
            case 7: break;
            case 8:
                $contact = $em->find('ZectranetBundle:User', $sourceID);
                $new_request->setContact($contact);
                break;
        }

        $em->persist($new_request);
        $em->flush();
        return $new_request;
    }

    /**
     * @param EntityManager $em
     * @param int $request_id
     * @param int $status_id
     */
    public static function changeRequestState (EntityManager $em, $request_id, $status_id) {
        $request = $em->find('ZectranetBundle:Request', $request_id);
        $status = $em->find('ZectranetBundle:RequestStatus', $status_id);
        $request->setStatus($status);
        $em->persist($request);
        $em->flush();
    }


    public static function getSentRequestsByUserID(EntityManager $em, $user_id) {
        $qb = $em->createQueryBuilder();
        $query = $qb->select('r')
            ->from('ZectranetBundle:Request', 'r')
            ->where('r.userid = :user_id')
            ->andWhere('r.statusID = 1')
            ->setParameter('user_id', $user_id)
            ->getQuery();
        return EntityOperations::arrayToJsonArray($query->getResult());
    }

    /**
     * Set contactID
     *
     * @param integer $contactID
     * @return Request
     */
    public function setContactID($contactID)
    {
        $this->contactID = $contactID;

        return $this;
    }

    /**
     * Get contactID
     *
     * @return integer 
     */
    public function getContactID()
    {
        return $this->contactID;
    }

    /**
     * Set contact
     *
     * @param \ZectranetBundle\Entity\User $contact
     * @return Request
     */
    public function setContact(\ZectranetBundle\Entity\User $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \ZectranetBundle\Entity\User 
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Request
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set statusID
     *
     * @param integer $statusID
     * @return Request
     */
    public function setStatusID($statusID)
    {
        $this->statusID = $statusID;

        return $this;
    }

    /**
     * Get statusID
     *
     * @return integer 
     */
    public function getStatusID()
    {
        return $this->statusID;
    }

    /**
     * Set status
     *
     * @param \ZectranetBundle\Entity\RequestStatus $status
     * @return Request
     */
    public function setStatus(\ZectranetBundle\Entity\RequestStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \ZectranetBundle\Entity\RequestStatus 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set HFForumID
     *
     * @param integer $hFForumID
     * @return Request
     */
    public function setHFForumID($hFForumID)
    {
        $this->HFForumID = $hFForumID;

        return $this;
    }

    /**
     * Get HFForumID
     *
     * @return integer 
     */
    public function getHFForumID()
    {
        return $this->HFForumID;
    }

    /**
     * Set QnAForumID
     *
     * @param integer $qnAForumID
     * @return Request
     */
    public function setQnAForumID($qnAForumID)
    {
        $this->QnAForumID = $qnAForumID;

        return $this;
    }

    /**
     * Get QnAForumID
     *
     * @return integer 
     */
    public function getQnAForumID()
    {
        return $this->QnAForumID;
    }

    /**
     * Set HFForum
     *
     * @param \ZectranetBundle\Entity\HFForum $hFForum
     * @return Request
     */
    public function setHFForum(\ZectranetBundle\Entity\HFForum $hFForum = null)
    {
        $this->HFForum = $hFForum;

        return $this;
    }

    /**
     * Get HFForum
     *
     * @return \ZectranetBundle\Entity\HFForum 
     */
    public function getHFForum()
    {
        return $this->HFForum;
    }

    /**
     * Set QnAForum
     *
     * @param \ZectranetBundle\Entity\QnAForum $qnAForum
     * @return Request
     */
    public function setQnAForum(\ZectranetBundle\Entity\QnAForum $qnAForum = null)
    {
        $this->QnAForum = $qnAForum;

        return $this;
    }

    /**
     * Get QnAForum
     *
     * @return \ZectranetBundle\Entity\QnAForum 
     */
    public function getQnAForum()
    {
        return $this->QnAForum;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Request
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }
}
