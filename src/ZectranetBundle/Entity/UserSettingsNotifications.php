<?php

namespace ZectranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Document
 *
 * @ORM\Table(name="user_settings_notifications")
 * @ORM\Entity
 */
class UserSettingsNotifications
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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userid;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="userSettingsNotifications")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @var User
     */
    protected $user;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_message_office", type="boolean", options={"default" = true})
     */
    private $msgEmailMessageOffice = true;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_message_topic", type="boolean", options={"default" = true})
     */
    private $msgEmailMessageTopic = true;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_removed_office", type="boolean", options={"default" = true})
     */
    private $msgEmailRemovedOffice = true;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_removed_topic", type="boolean", options={"default" = true})
     */
    private $msgEmailRemovedTopic = true;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_topic_added", type="boolean", options={"default" = true})
     */
    private $msgEmailTopicAdd = true;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_task_assigned", type="boolean", options={"default" = true})
     */
    private $msgEmailTaskAssigned = true;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_task_comment", type="boolean", options={"default" = true})
     */
    private $msgEmailTaskComment = true;

    /**
     * @var boolean
     * @ORM\Column(name="msg_site_message_office", type="boolean", options={"default" = true})
     */
    private $msgSiteMessageOffice = true;

    /**
     * @var boolean
     * @ORM\Column(name="msg_site_removed_office", type="boolean", options={"default" = true})
     */
    private $msgSiteRemovedOffice = true;

    /**
     * @var boolean
     * @ORM\Column(name="msg_site_task_assigned", type="boolean", options={"default" = true})
     */
    private $msgSiteTaskAssigned = true;

    /**
     * @var boolean
     * @ORM\Column(name="msg_site_task_comment", type="boolean", options={"default" = true})
     */
    private $msgSiteTaskComment = true;

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
     * @return UserSettingsNotifications
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
     * Set msgEmailMessageOffice
     *
     * @param boolean $msgEmailMessageOffice
     * @return UserSettingsNotifications
     */
    public function setMsgEmailMessageOffice($msgEmailMessageOffice)
    {
        $this->msgEmailMessageOffice = $msgEmailMessageOffice;

        return $this;
    }

    /**
     * Get msgEmailMessageOffice
     *
     * @return boolean 
     */
    public function getMsgEmailMessageOffice()
    {
        return $this->msgEmailMessageOffice;
    }

    /**
     * Set msgEmailMessageTopic
     *
     * @param boolean $msgEmailMessageTopic
     * @return UserSettingsNotifications
     */
    public function setMsgEmailMessageTopic($msgEmailMessageTopic)
    {
        $this->msgEmailMessageTopic = $msgEmailMessageTopic;

        return $this;
    }

    /**
     * Get msgEmailMessageTopic
     *
     * @return boolean 
     */
    public function getMsgEmailMessageTopic()
    {
        return $this->msgEmailMessageTopic;
    }

    /**
     * Set msgEmailRemovedOffice
     *
     * @param boolean $msgEmailRemovedOffice
     * @return UserSettingsNotifications
     */
    public function setMsgEmailRemovedOffice($msgEmailRemovedOffice)
    {
        $this->msgEmailRemovedOffice = $msgEmailRemovedOffice;

        return $this;
    }

    /**
     * Get msgEmailRemovedOffice
     *
     * @return boolean 
     */
    public function getMsgEmailRemovedOffice()
    {
        return $this->msgEmailRemovedOffice;
    }

    /**
     * Set msgEmailRemovedTopic
     *
     * @param boolean $msgEmailRemovedTopic
     * @return UserSettingsNotifications
     */
    public function setMsgEmailRemovedTopic($msgEmailRemovedTopic)
    {
        $this->msgEmailRemovedTopic = $msgEmailRemovedTopic;

        return $this;
    }

    /**
     * Get msgEmailRemovedTopic
     *
     * @return boolean 
     */
    public function getMsgEmailRemovedTopic()
    {
        return $this->msgEmailRemovedTopic;
    }

    /**
     * Set msgEmailTopicAdd
     *
     * @param boolean $msgEmailTopicAdd
     * @return UserSettingsNotifications
     */
    public function setMsgEmailTopicAdd($msgEmailTopicAdd)
    {
        $this->msgEmailTopicAdd = $msgEmailTopicAdd;

        return $this;
    }

    /**
     * Get msgEmailTopicAdd
     *
     * @return boolean 
     */
    public function getMsgEmailTopicAdd()
    {
        return $this->msgEmailTopicAdd;
    }

    /**
     * Set msgEmailTaskAssigned
     *
     * @param boolean $msgEmailTaskAssigned
     * @return UserSettingsNotifications
     */
    public function setMsgEmailTaskAssigned($msgEmailTaskAssigned)
    {
        $this->msgEmailTaskAssigned = $msgEmailTaskAssigned;

        return $this;
    }

    /**
     * Get msgEmailTaskAssigned
     *
     * @return boolean 
     */
    public function getMsgEmailTaskAssigned()
    {
        return $this->msgEmailTaskAssigned;
    }

    /**
     * Set msgEmailTaskComment
     *
     * @param boolean $msgEmailTaskComment
     * @return UserSettingsNotifications
     */
    public function setMsgEmailTaskComment($msgEmailTaskComment)
    {
        $this->msgEmailTaskComment = $msgEmailTaskComment;

        return $this;
    }

    /**
     * Get msgEmailTaskComment
     *
     * @return boolean 
     */
    public function getMsgEmailTaskComment()
    {
        return $this->msgEmailTaskComment;
    }

    /**
     * Set msgSiteMessageOffice
     *
     * @param boolean $msgSiteMessageOffice
     * @return UserSettingsNotifications
     */
    public function setMsgSiteMessageOffice($msgSiteMessageOffice)
    {
        $this->msgSiteMessageOffice = $msgSiteMessageOffice;

        return $this;
    }

    /**
     * Get msgSiteMessageOffice
     *
     * @return boolean 
     */
    public function getMsgSiteMessageOffice()
    {
        return $this->msgSiteMessageOffice;
    }

    /**
     * Set msgSiteRemovedOffice
     *
     * @param boolean $msgSiteRemovedOffice
     * @return UserSettingsNotifications
     */
    public function setMsgSiteRemovedOffice($msgSiteRemovedOffice)
    {
        $this->msgSiteRemovedOffice = $msgSiteRemovedOffice;

        return $this;
    }

    /**
     * Get msgSiteRemovedOffice
     *
     * @return boolean 
     */
    public function getMsgSiteRemovedOffice()
    {
        return $this->msgSiteRemovedOffice;
    }

    /**
     * Set msgSiteTaskAssigned
     *
     * @param boolean $msgSiteTaskAssigned
     * @return UserSettingsNotifications
     */
    public function setMsgSiteTaskAssigned($msgSiteTaskAssigned)
    {
        $this->msgSiteTaskAssigned = $msgSiteTaskAssigned;

        return $this;
    }

    /**
     * Get msgSiteTaskAssigned
     *
     * @return boolean 
     */
    public function getMsgSiteTaskAssigned()
    {
        return $this->msgSiteTaskAssigned;
    }

    /**
     * Set msgSiteTaskComment
     *
     * @param boolean $msgSiteTaskComment
     * @return UserSettingsNotifications
     */
    public function setMsgSiteTaskComment($msgSiteTaskComment)
    {
        $this->msgSiteTaskComment = $msgSiteTaskComment;

        return $this;
    }

    /**
     * Get msgSiteTaskComment
     *
     * @return boolean 
     */
    public function getMsgSiteTaskComment()
    {
        return $this->msgSiteTaskComment;
    }

    /**
     * Set user
     *
     * @param \ZectranetBundle\Entity\User $user
     * @return UserSettingsNotifications
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
}
