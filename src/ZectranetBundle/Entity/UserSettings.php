<?php

namespace ZectranetBundle\Entity;

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
     * @ORM\Column(name="show_hidden_topics", type="boolean", options={"default" = true})
     */
    private $showHiddenTopics;

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

    public function __construct() {
        $this->disableAllOnEmail = false;
        $this->disableAllOnSite = false;
        $this->showHiddenTopics = true;
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
     * Set showHiddenTopics
     *
     * @param boolean $showHiddenTopics
     * @return UserSettings
     */
    public function setShowHiddenTopics($showHiddenTopics)
    {
        $this->showHiddenTopics = $showHiddenTopics;

        return $this;
    }

    /**
     * Get showHiddenTopics
     *
     * @return boolean 
     */
    public function getShowHiddenTopics()
    {
        return $this->showHiddenTopics;
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
}
