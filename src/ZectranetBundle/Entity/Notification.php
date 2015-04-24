<?php

namespace ZectranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table(name="notifications")
 * @ORM\Entity
 */
class Notification
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
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userid;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="notifications")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @var User
     */
    private $user;

    /**
     * @var integer
     * @ORM\Column(name="resource_id", type="integer")
     */
    private $resourceid;

    /**
     * @var integer
     * @ORM\Column(name="destination_id", type="integer")
     */
    private $destinationid;

    /**
     * @var integer
     * @ORM\Column(name="conversation_id", type="integer", nullable=true, options={"default":NULL})
     */
    private $conversation_id;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", length=100)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var \DateTime
     * @ORM\Column(name="activated", type="datetime")
     */
    private $activated;
    

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
     * Set userid
     *
     * @param integer $userid
     * @return Notification
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
     * Set resourceid
     *
     * @param integer $resourceid
     * @return Notification
     */
    public function setResourceid($resourceid)
    {
        $this->resourceid = $resourceid;

        return $this;
    }

    /**
     * Get resourceid
     *
     * @return integer 
     */
    public function getResourceid()
    {
        return $this->resourceid;
    }

    /**
     * Set destinationid
     *
     * @param integer $destinationid
     * @return Notification
     */
    public function setDestinationid($destinationid)
    {
        $this->destinationid = $destinationid;

        return $this;
    }

    /**
     * Get destinationid
     *
     * @return integer 
     */
    public function getDestinationid()
    {
        return $this->destinationid;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Notification
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Notification
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
     * Set activated
     *
     * @param \DateTime $activated
     * @return Notification
     */
    public function setActivated($activated)
    {
        $this->activated = $activated;

        return $this;
    }

    /**
     * Get activated
     *
     * @return \DateTime 
     */
    public function getActivated()
    {
        return $this->activated;
    }

    /**
     * Set user
     *
     * @param \ZectranetBundle\Entity\User $user
     * @return Notification
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
     * @return array
     */
    public function getInArray()
    {
        return array(
            'id' => $this->getId(),
            'user' => $this->getUser()->getInArray(),
            'resourceid' => $this->getResourceid(),
            'destinationid' => $this->getDestinationid(),
            'conversationid' => $this->getConversationId(),
            'type' => $this->getType(),
            'message' => $this->getMessage(),
            'activated' => $this->getActivated()
        );
    }

    /**
     * @param User $user
     * @return array
     */
    public static function prepareNotifications($user)
    {
        /** @var UserSettings $user_settings */
        $user_settings = $user->getUserSettings();

        /** @var array $user_notifications */
        $user_notifications = $user->getNotifications();

        $temp_user_notifications = array();

        if ($user_settings->getDisableAllOnSite() == false)
        {
            for ($i = 0; $i < count($user_notifications); $i++)
            {
                $method = null;

                switch ($user_notifications[$i]->getType())
                {
                    case "message_office":
                        $method = $user_settings->getMsgSiteMessageOffice();
                        break;

                    case "message_home_office":
                        $method = $user_settings->getMsgSiteMessageOffice();
                        break;

                    case "message_project":
                        $method = $user_settings->getMsgSiteMessageProject();
                        break;

                    case "message_epic_story":
                        $method = $user_settings->getMsgSiteMessageEpicStory();
                        break;

                    case "message_task":
                        $method = $user_settings->getMsgSiteMessageTask();
                        break;

                    //-----------------------------------------------------------------

                    case "task_added":
                        $method = $user_settings->getMsgSiteTaskAdded();
                        break;

                    case "epic_story_added":
                        $method = $user_settings->getMsgSiteEpicStoryAdded();
                        break;

                    case "task_deleted":
                        $method = $user_settings->getMsgSiteTaskDeleted();
                        break;

                    case "epic_story_deleted":
                        $method = $user_settings->getMsgSiteEpicStoryDeleted();
                        break;
                }

                if (in_array($user_notifications[$i]->getType(), array("private_message_office", "private_message_project", "private_message_epic_story", "private_message_task")))
                    $method = true;

                if (in_array($user_notifications[$i]->getType(), array("request_office", "request_user_project", "request_project", "request_assign_task")))
                    $method = false;

                if ($method == true)
                    $temp_user_notifications[] = $user_notifications[$i];
            }
        }

        else $temp_user_notifications = null;

        return $temp_user_notifications;
    }

    /**
     * Set conversation_id
     *
     * @param integer $conversationId
     * @return Notification
     */
    public function setConversationId($conversationId)
    {
        $this->conversation_id = $conversationId;

        return $this;
    }

    /**
     * Get conversation_id
     *
     * @return integer 
     */
    public function getConversationId()
    {
        return $this->conversation_id;
    }
}
