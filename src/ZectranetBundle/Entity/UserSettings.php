<?php

namespace ZectranetBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * UserSettings
 *
 * @ORM\Table(name="user_settings")
 * @ORM\Entity
 */
class UserSettings
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userid;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="userSettings")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @var User
     */
    protected $user;

    /**
     * @var boolean
     * @ORM\Column(name="show_closed_projects", type="boolean", options={"default" = true})
     */
    private $showClosedProjects;

    /**
     * @var boolean
     * @ORM\Column(name="disable_message_on_email", type="boolean", options={"default" = false})
     */
    private $disableAllOnEmail;

    /**
     * @var boolean
     * @ORM\Column(name="disable_message_on_site", type="boolean", options={"default" = false})
     */
    private $disableAllOnSite;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_message_office", type="boolean", options={"default" = true})
     */
    private $msgEmailMessageOffice;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_message_project", type="boolean", options={"default" = true})
     */
    private $msgEmailMessageProject;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_removed_office", type="boolean", options={"default" = true})
     */
    private $msgEmailRemovedOffice;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_removed_project", type="boolean", options={"default" = true})
     */
    private $msgEmailRemovedProject;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_task_assigned", type="boolean", options={"default" = true})
     */
    private $msgEmailTaskAssigned;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_task_comment", type="boolean", options={"default" = true})
     */
    private $msgEmailTaskComment;


    /**
     * @var boolean
     * @ORM\Column(name="msg_Site_message_office", type="boolean", options={"default" = true})
     */
    private $msgSiteMessageOffice;

    /**
     * @var boolean
     * @ORM\Column(name="msg_Site_message_project", type="boolean", options={"default" = true})
     */
    private $msgSiteMessageProject;

    /**
     * @var boolean
     * @ORM\Column(name="msg_Site_removed_office", type="boolean", options={"default" = true})
     */
    private $msgSiteRemovedOffice;

    /**
     * @var boolean
     * @ORM\Column(name="msg_Site_removed_project", type="boolean", options={"default" = true})
     */
    private $msgSiteRemovedProject;

    /**
     * @var boolean
     * @ORM\Column(name="msg_Site_task_assigned", type="boolean", options={"default" = true})
     */
    private $msgSiteTaskAssigned;

    /**
     * @var boolean
     * @ORM\Column(name="msg_Site_task_comment", type="boolean", options={"default" = true})
     */
    private $msgSiteTaskComment;


    public function __construct() {
        $this->disableAllOnEmail = false;
        $this->disableAllOnSite = false;

        $this->showClosedProjects = true;
        $this->msgEmailMessageOffice = true;
        $this->msgEmailMessageProject = true;
        $this->msgEmailRemovedOffice = true;
        $this->msgEmailRemovedProject = true;
        $this->msgEmailTaskAssigned = true;
        $this->msgEmailTaskComment = true;
        $this->msgSiteMessageOffice = true;
        $this->msgSiteMessageProject = true;
        $this->msgSiteRemovedOffice = true;
        $this->msgSiteRemovedProject = true;
        $this->msgSiteTaskAssigned = true;
        $this->msgSiteTaskComment = true;
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
     * Set userid
     *
     * @param integer $userid
     * @return UserSettings
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
     * Set showClosedProjects
     *
     * @param boolean $showClosedProjects
     * @return UserSettings
     */
    public function setShowClosedProjects($showClosedProjects)
    {
        $this->showClosedProjects = $showClosedProjects;

        return $this;
    }

    /**
     * Get showClosedProjects
     *
     * @return boolean 
     */
    public function getShowClosedProjects()
    {
        return $this->showClosedProjects;
    }

    /**
     * Set disableAllOnEmail
     *
     * @param boolean $disableAllOnEmail
     * @return UserSettings
     */
    public function setDisableAllOnEmail($disableAllOnEmail)
    {
        $this->disableAllOnEmail = $disableAllOnEmail;

        return $this;
    }

    /**
     * Get disableAllOnEmail
     *
     * @return boolean 
     */
    public function getDisableAllOnEmail()
    {
        return $this->disableAllOnEmail;
    }

    /**
     * Set disableAllOnSite
     *
     * @param boolean $disableAllOnSite
     * @return UserSettings
     */
    public function setDisableAllOnSite($disableAllOnSite)
    {
        $this->disableAllOnSite = $disableAllOnSite;

        return $this;
    }

    /**
     * Get disableAllOnSite
     *
     * @return boolean 
     */
    public function getDisableAllOnSite()
    {
        return $this->disableAllOnSite;
    }

    /**
     * Set msgEmailMessageOffice
     *
     * @param boolean $msgEmailMessageOffice
     * @return UserSettings
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
     * Set msgEmailMessageProject
     *
     * @param boolean $msgEmailMessageProject
     * @return UserSettings
     */
    public function setMsgEmailMessageProject($msgEmailMessageProject)
    {
        $this->msgEmailMessageProject = $msgEmailMessageProject;

        return $this;
    }

    /**
     * Get msgEmailMessageProject
     *
     * @return boolean 
     */
    public function getMsgEmailMessageProject()
    {
        return $this->msgEmailMessageProject;
    }

    /**
     * Set msgEmailRemovedOffice
     *
     * @param boolean $msgEmailRemovedOffice
     * @return UserSettings
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
     * Set msgEmailRemovedProject
     *
     * @param boolean $msgEmailRemovedProject
     * @return UserSettings
     */
    public function setMsgEmailRemovedProject($msgEmailRemovedProject)
    {
        $this->msgEmailRemovedProject = $msgEmailRemovedProject;

        return $this;
    }

    /**
     * Get msgEmailRemovedProject
     *
     * @return boolean 
     */
    public function getMsgEmailRemovedProject()
    {
        return $this->msgEmailRemovedProject;
    }

    /**
     * Set msgEmailTaskAssigned
     *
     * @param boolean $msgEmailTaskAssigned
     * @return UserSettings
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
     * @return UserSettings
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
     * @return UserSettings
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
     * Set msgSiteMessageProject
     *
     * @param boolean $msgSiteMessageProject
     * @return UserSettings
     */
    public function setMsgSiteMessageProject($msgSiteMessageProject)
    {
        $this->msgSiteMessageProject = $msgSiteMessageProject;

        return $this;
    }

    /**
     * Get msgSiteMessageProject
     *
     * @return boolean 
     */
    public function getMsgSiteMessageProject()
    {
        return $this->msgSiteMessageProject;
    }

    /**
     * Set msgSiteRemovedOffice
     *
     * @param boolean $msgSiteRemovedOffice
     * @return UserSettings
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
     * Set msgSiteRemovedProject
     *
     * @param boolean $msgSiteRemovedProject
     * @return UserSettings
     */
    public function setMsgSiteRemovedProject($msgSiteRemovedProject)
    {
        $this->msgSiteRemovedProject = $msgSiteRemovedProject;

        return $this;
    }

    /**
     * Get msgSiteRemovedProject
     *
     * @return boolean 
     */
    public function getMsgSiteRemovedProject()
    {
        return $this->msgSiteRemovedProject;
    }

    /**
     * Set msgSiteTaskAssigned
     *
     * @param boolean $msgSiteTaskAssigned
     * @return UserSettings
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
     * @return UserSettings
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
     * @return UserSettings
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
     * @param UserSettings $settings
     * @param array $parameters
     */
    public static function setEmailSettings($em, $settings, $parameters)
    {
        $settings->setDisableAllOnEmail(($parameters['disableAllOnEmail'] == null) ? false : true);
        $settings->setMsgEmailMessageOffice(($parameters['msgEmailMessageOffice'] == null) ? false : true);
        $settings->setMsgEmailMessageProject(($parameters['msgEmailMessageProject'] == null) ? false : true);
        $settings->setMsgEmailRemovedOffice(($parameters['msgEmailRemovedOffice'] == null) ? false : true);
        $settings->setMsgEmailRemovedProject(($parameters['msgEmailRemovedProject'] == null) ? false : true);
        $settings->setMsgEmailTaskAssigned(($parameters['msgEmailTaskAssigned'] == null) ? false : true);
        $settings->setMsgEmailTaskComment(($parameters['msgEmailTaskComment'] == null) ? false : true);

        $em->persist($settings);
        $em->flush();
    }

    /**
     * @param EntityManager $em
     * @param UserSettings $settings
     * @param array $parameters
     */
    public static function setSiteSettings($em, $settings, $parameters)
    {
        $settings->setDisableAllOnSite(($parameters['disableAllOnSite'] == null) ? false : true);
        $settings->setMsgSiteMessageOffice(($parameters['msgSiteMessageOffice'] == null) ? false : true);
        $settings->setMsgSiteMessageProject(($parameters['msgSiteMessageProject'] == null) ? false : true);
        $settings->setMsgSiteRemovedOffice(($parameters['msgSiteRemovedOffice'] == null) ? false : true);
        $settings->setMsgSiteRemovedProject(($parameters['msgSiteRemovedProject'] == null) ? false : true);
        $settings->setMsgSiteTaskAssigned(($parameters['msgSiteTaskAssigned'] == null) ? false : true);
        $settings->setMsgSiteTaskComment(($parameters['msgSiteTaskComment'] == null) ? false : true);

        $em->persist($settings);
        $em->flush();
    }
}
