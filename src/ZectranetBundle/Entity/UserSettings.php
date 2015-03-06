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
     * @ORM\Column(name="msg_email_message_epic_story", type="boolean", options={"default" = true})
     */
    private $msgEmailMessageEpicStory;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_message_task", type="boolean", options={"default" = true})
     */
    private $msgEmailMessageTask;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_task_added", type="boolean", options={"default" = true})
     */
    private $msgEmailTaskAdded;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_epic_story_added", type="boolean", options={"default" = true})
     */
    private $msgEmailEpicStoryAdded;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_task_deleted", type="boolean", options={"default" = true})
     */
    private $msgEmailTaskDeleted;

    /**
     * @var boolean
     * @ORM\Column(name="msg_email_epic_story_deleted", type="boolean", options={"default" = true})
     */
    private $msgEmailEpicStoryDeleted;


    /**
     * @var boolean
     * @ORM\Column(name="msg_site_message_office", type="boolean", options={"default" = true})
     */
    private $msgSiteMessageOffice;

    /**
     * @var boolean
     * @ORM\Column(name="msg_site_message_project", type="boolean", options={"default" = true})
     */
    private $msgSiteMessageProject;

    /**
     * @var boolean
     * @ORM\Column(name="msg_site_message_epic_story", type="boolean", options={"default" = true})
     */
    private $msgSiteMessageEpicStory;

    /**
     * @var boolean
     * @ORM\Column(name="msg_site_message_task", type="boolean", options={"default" = true})
     */
    private $msgSiteMessageTask;

    /**
     * @var boolean
     * @ORM\Column(name="msg_site_task_added", type="boolean", options={"default" = true})
     */
    private $msgSiteTaskAdded;

    /**
     * @var boolean
     * @ORM\Column(name="msg_site_epic_story_added", type="boolean", options={"default" = true})
     */
    private $msgSiteEpicStoryAdded;

    /**
     * @var boolean
     * @ORM\Column(name="msg_site_task_deleted", type="boolean", options={"default" = true})
     */
    private $msgSiteTaskDeleted;

    /**
     * @var boolean
     * @ORM\Column(name="msg_site_epic_story_deleted", type="boolean", options={"default" = true})
     */
    private $msgSiteEpicStoryDeleted;

    function __construct()
    {
        $this->showClosedProjects = true;
        $this->disableAllOnEmail = false;
        $this->disableAllOnSite = false;
        $this->msgEmailMessageOffice = true;
        $this->msgEmailMessageProject = true;
        $this->msgEmailMessageEpicStory = true;
        $this->msgEmailMessageTask = true;
        $this->msgEmailTaskAdded = true;
        $this->msgEmailEpicStoryAdded = true;
        $this->msgEmailTaskDeleted = true;
        $this->msgEmailEpicStoryDeleted = true;
        $this->msgSiteMessageOffice = true;
        $this->msgSiteMessageProject = true;
        $this->msgSiteMessageEpicStory = true;
        $this->msgSiteMessageTask = true;
        $this->msgSiteTaskAdded = true;
        $this->msgSiteEpicStoryAdded = true;
        $this->msgSiteTaskDeleted = true;
        $this->msgSiteEpicStoryDeleted = true;
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
     * Set msgEmailMessageEpicStory
     *
     * @param boolean $msgEmailMessageEpicStory
     * @return UserSettings
     */
    public function setMsgEmailMessageEpicStory($msgEmailMessageEpicStory)
    {
        $this->msgEmailMessageEpicStory = $msgEmailMessageEpicStory;

        return $this;
    }

    /**
     * Get msgEmailMessageEpicStory
     *
     * @return boolean 
     */
    public function getMsgEmailMessageEpicStory()
    {
        return $this->msgEmailMessageEpicStory;
    }

    /**
     * Set msgEmailMessageTask
     *
     * @param boolean $msgEmailMessageTask
     * @return UserSettings
     */
    public function setMsgEmailMessageTask($msgEmailMessageTask)
    {
        $this->msgEmailMessageTask = $msgEmailMessageTask;

        return $this;
    }

    /**
     * Get msgEmailMessageTask
     *
     * @return boolean 
     */
    public function getMsgEmailMessageTask()
    {
        return $this->msgEmailMessageTask;
    }

    /**
     * Set msgEmailTaskAdded
     *
     * @param boolean $msgEmailTaskAdded
     * @return UserSettings
     */
    public function setMsgEmailTaskAdded($msgEmailTaskAdded)
    {
        $this->msgEmailTaskAdded = $msgEmailTaskAdded;

        return $this;
    }

    /**
     * Get msgEmailTaskAdded
     *
     * @return boolean 
     */
    public function getMsgEmailTaskAdded()
    {
        return $this->msgEmailTaskAdded;
    }

    /**
     * Set msgEmailEpicStoryAdded
     *
     * @param boolean $msgEmailEpicStoryAdded
     * @return UserSettings
     */
    public function setMsgEmailEpicStoryAdded($msgEmailEpicStoryAdded)
    {
        $this->msgEmailEpicStoryAdded = $msgEmailEpicStoryAdded;

        return $this;
    }

    /**
     * Get msgEmailEpicStoryAdded
     *
     * @return boolean 
     */
    public function getMsgEmailEpicStoryAdded()
    {
        return $this->msgEmailEpicStoryAdded;
    }

    /**
     * Set msgEmailTaskDeleted
     *
     * @param boolean $msgEmailTaskDeleted
     * @return UserSettings
     */
    public function setMsgEmailTaskDeleted($msgEmailTaskDeleted)
    {
        $this->msgEmailTaskDeleted = $msgEmailTaskDeleted;

        return $this;
    }

    /**
     * Get msgEmailTaskDeleted
     *
     * @return boolean 
     */
    public function getMsgEmailTaskDeleted()
    {
        return $this->msgEmailTaskDeleted;
    }

    /**
     * Set msgEmailEpicStoryDeleted
     *
     * @param boolean $msgEmailEpicStoryDeleted
     * @return UserSettings
     */
    public function setMsgEmailEpicStoryDeleted($msgEmailEpicStoryDeleted)
    {
        $this->msgEmailEpicStoryDeleted = $msgEmailEpicStoryDeleted;

        return $this;
    }

    /**
     * Get msgEmailEpicStoryDeleted
     *
     * @return boolean 
     */
    public function getMsgEmailEpicStoryDeleted()
    {
        return $this->msgEmailEpicStoryDeleted;
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
     * Set msgSiteMessageEpicStory
     *
     * @param boolean $msgSiteMessageEpicStory
     * @return UserSettings
     */
    public function setMsgSiteMessageEpicStory($msgSiteMessageEpicStory)
    {
        $this->msgSiteMessageEpicStory = $msgSiteMessageEpicStory;

        return $this;
    }

    /**
     * Get msgSiteMessageEpicStory
     *
     * @return boolean 
     */
    public function getMsgSiteMessageEpicStory()
    {
        return $this->msgSiteMessageEpicStory;
    }

    /**
     * Set msgSiteMessageTask
     *
     * @param boolean $msgSiteMessageTask
     * @return UserSettings
     */
    public function setMsgSiteMessageTask($msgSiteMessageTask)
    {
        $this->msgSiteMessageTask = $msgSiteMessageTask;

        return $this;
    }

    /**
     * Get msgSiteMessageTask
     *
     * @return boolean 
     */
    public function getMsgSiteMessageTask()
    {
        return $this->msgSiteMessageTask;
    }

    /**
     * Set msgSiteTaskAdded
     *
     * @param boolean $msgSiteTaskAdded
     * @return UserSettings
     */
    public function setMsgSiteTaskAdded($msgSiteTaskAdded)
    {
        $this->msgSiteTaskAdded = $msgSiteTaskAdded;

        return $this;
    }

    /**
     * Get msgSiteTaskAdded
     *
     * @return boolean 
     */
    public function getMsgSiteTaskAdded()
    {
        return $this->msgSiteTaskAdded;
    }

    /**
     * Set msgSiteEpicStoryAdded
     *
     * @param boolean $msgSiteEpicStoryAdded
     * @return UserSettings
     */
    public function setMsgSiteEpicStoryAdded($msgSiteEpicStoryAdded)
    {
        $this->msgSiteEpicStoryAdded = $msgSiteEpicStoryAdded;

        return $this;
    }

    /**
     * Get msgSiteEpicStoryAdded
     *
     * @return boolean 
     */
    public function getMsgSiteEpicStoryAdded()
    {
        return $this->msgSiteEpicStoryAdded;
    }

    /**
     * Set msgSiteTaskDeleted
     *
     * @param boolean $msgSiteTaskDeleted
     * @return UserSettings
     */
    public function setMsgSiteTaskDeleted($msgSiteTaskDeleted)
    {
        $this->msgSiteTaskDeleted = $msgSiteTaskDeleted;

        return $this;
    }

    /**
     * Get msgSiteTaskDeleted
     *
     * @return boolean 
     */
    public function getMsgSiteTaskDeleted()
    {
        return $this->msgSiteTaskDeleted;
    }

    /**
     * Set msgSiteEpicStoryDeleted
     *
     * @param boolean $msgSiteEpicStoryDeleted
     * @return UserSettings
     */
    public function setMsgSiteEpicStoryDeleted($msgSiteEpicStoryDeleted)
    {
        $this->msgSiteEpicStoryDeleted = $msgSiteEpicStoryDeleted;

        return $this;
    }

    /**
     * Get msgSiteEpicStoryDeleted
     *
     * @return boolean 
     */
    public function getMsgSiteEpicStoryDeleted()
    {
        return $this->msgSiteEpicStoryDeleted;
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
        $settings->setMsgEmailMessageEpicStory(($parameters['msgEmailMessageEpicStory'] == null) ? false : true);
        $settings->setMsgEmailMessageTask(($parameters['msgEmailMessageTask'] == null) ? false : true);
        $settings->setMsgEmailTaskAdded(($parameters['msgEmailTaskAdded'] == null) ? false : true);
        $settings->setMsgEmailEpicStoryAdded(($parameters['msgEmailEpicStoryAdded'] == null) ? false : true);
        $settings->setMsgEmailTaskDeleted(($parameters['msgEmailTaskDeleted'] == null) ? false : true);
        $settings->setMsgEmailEpicStoryDeleted(($parameters['msgEmailEpicStoryDeleted'] == null) ? false : true);

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
        $settings->setMsgSiteMessageEpicStory(($parameters['msgSiteMessageEpicStory'] == null) ? false : true);
        $settings->setMsgSiteMessageTask(($parameters['msgSiteMessageTask'] == null) ? false : true);
        $settings->setMsgSiteTaskAdded(($parameters['msgSiteTaskAdded'] == null) ? false : true);
        $settings->setMsgSiteEpicStoryAdded(($parameters['msgSiteEpicStoryAdded'] == null) ? false : true);
        $settings->setMsgSiteTaskDeleted(($parameters['msgSiteTaskDeleted'] == null) ? false : true);
        $settings->setMsgSiteEpicStoryDeleted(($parameters['msgSiteEpicStoryDeleted'] == null) ? false : true);

        $em->persist($settings);
        $em->flush();
    }
}
