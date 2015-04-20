<?php

namespace ZectranetBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Join;
use ZectranetBundle\Entity\Request;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\Office;
use ZectranetBundle\Entity\Notification;

/**
 * UserInfo
 *
 * @ORM\Table(name="users_info")
 * @ORM\Entity
 */
class UserInfo
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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userID;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="User", inversedBy="userInfo")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(name="residence_country", type="string", length=255, nullable=true, options={"default" = null})
     */
    private $residenceCountry;

    /**
     * @var boolean
     * @ORM\Column(name="residence_country_visible", type="boolean", options={"default" = true})
     */
    private $residenceCountryVisible;

    /**
     * @var string
     * @ORM\Column(name="work_expirience", type="string", length=255, nullable=true, options={"default" = null})
     */
    private $workExpirience;

    /**
     * @var boolean
     * @ORM\Column(name="work_expirience_visible", type="boolean", nullable=true, options={"default" = true})
     */
    private $workExpirienceVisible;

    /**
     * @var string
     * @ORM\Column(name="skills", type="string", length=255, nullable=true, options={"default" = null})
     */
    private $skills;

    /**
     * @var boolean
     * @ORM\Column(name="skills_visible", type="boolean", options={"default" = true})
     */
    private $skillsVisible;

    /**
     * @var string
     * @ORM\Column(name="interests", type="string", length=255, nullable=true, options={"default" = null})
     */
    private $interests;

    /**
     * @var boolean
     * @ORM\Column(name="interests_visible", type="boolean", options={"default" = true})
     */
    private $interestsVisible;

    /**
     * @var string
     * @ORM\Column(name="volunteer_work", type="string", length=255, nullable=true, options={"default" = null})
     */
    private $volunteerWork;

    /**
     * @var boolean
     * @ORM\Column(name="volunteer_work_visible", type="boolean", options={"default" = true})
     */
    private $volunteerWorkVisible;

    /**
     * @var string
     * @ORM\Column(name="societies", type="string", length=255, nullable=true, options={"default" = null})
     */
    private $societies;

    /**
     * @var boolean
     * @ORM\Column(name="societies_visible", type="boolean", options={"default" = true})
     */
    private $societiesVisible;

    /**
     * @var string
     * @ORM\Column(name="facebook", type="string", length=255, nullable=true, options={"default" = null})
     */
    private $facebook;

    /**
     * @var boolean
     * @ORM\Column(name="facebook_visible", type="boolean", options={"default" = true})
     */
    private $facebookVisible;

    /**
     * @var string
     * @ORM\Column(name="twitter", type="string", length=255, nullable=true, options={"default" = null})
     */
    private $twitter;

    /**
     * @var boolean
     * @ORM\Column(name="twitter_visible", type="boolean", options={"default" = true})
     */
    private $twitterVisible;

    /**
     * @var string
     * @ORM\Column(name="linked_in", type="string", length=255, nullable=true, options={"default" = null})
     */
    private $linkedIn;

    /**
     * @var boolean
     * @ORM\Column(name="linked_in_visible", type="boolean", options={"default" = true})
     */
    private $linkedInVisible;

    /**
     * @var string
     * @ORM\Column(name="google_plus", type="string", length=255, nullable=true, options={"default" = null})
     */
    private $googlePlus;

    /**
     * @var boolean
     * @ORM\Column(name="google_plus_visible", type="boolean", options={"default" = true})
     */
    private $googlePlusVisible;

    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'facebook' => $this->getFacebook(),
            'facebookVisible' => $this->getFacebookVisible(),
            'googlePlus' => $this->getGooglePlus(),
            'googlePlusVisible' => $this->getGooglePlusVisible(),
            'interests' => $this->getInterests(),
            'interestsVisible' => $this->getInterestsVisible(),
            'linkedIn' => $this->getLinkedIn(),
            'linkedInVisible' => $this->getLinkedInVisible(),
            'residenceCountry' => $this->getResidenceCountry(),
            'residenceCountryVisible' => $this->getResidenceCountryVisible(),
            'skills' => $this->getSkills(),
            'skillsVisible' => $this->getSkillsVisible(),
            'societies' => $this->getSocieties(),
            'societiesVisible' => $this->getSocietiesVisible(),
            'twitter' => $this->getTwitter(),
            'twitterVisible' => $this->getTwitterVisible(),
            'userID' => $this->getUserID(),
            'volunteerWork' => $this->getVolunteerWork(),
            'volunteerWorkVisible' => $this->getVolunteerWorkVisible(),
            'workExpirience' => $this->getWorkExpirience(),
            'workExpirienceVisible' => $this->getWorkExpirienceVisible(),
        );
    }

    /**
     * @param EntityManager $em
     * @param int $user_id
     * @param array $params
     * @return bool
     */
    public static function editInfo(EntityManager $em, $user_id, $params) {
        /** @var UserInfo $info */
        $info = $em->getRepository('ZectranetBundle:UserInfo')->findOneBy(array('userID' => $user_id));

        $info->setResidenceCountry($params['residenceCountry']);
        $info->setResidenceCountryVisible($params['residenceCountryVisible']);
        $info->setWorkExpirience($params['workExpirience']);
        $info->setWorkExpirienceVisible($params['workExpirienceVisible']);
        $info->setSkills($params['skills']);
        $info->setSkillsVisible($params['skillsVisible']);
        $info->setInterests($params['interests']);
        $info->setInterestsVisible($params['interestsVisible']);
        $info->setVolunteerWork($params['volunteerWork']);
        $info->setVolunteerWorkVisible($params['volunteerWorkVisible']);
        $info->setFacebook($params['facebook']);
        $info->setFacebookVisible($params['facebookVisible']);
        $info->setTwitter($params['twitter']);
        $info->setTwitterVisible($params['twitterVisible']);
        $info->setLinkedIn($params['linkedIn']);
        $info->setLinkedInVisible($params['linkedInVisible']);
        $info->setGooglePlus($params['googlePlus']);
        $info->setGooglePlusVisible($params['googlePlusVisible']);

        $em->persist($info);
        $em->flush();
    }

    public function __construct() {
        $this->facebookVisible = true;
        $this->interestsVisible = true;
        $this->skillsVisible = true;
        $this->societiesVisible = true;
        $this->twitterVisible = true;
        $this->workExpirienceVisible = true;
        $this->googlePlusVisible = true;
        $this->linkedInVisible = true;
        $this->residenceCountryVisible = true;
        $this->volunteerWorkVisible = true;
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
     * Set userID
     *
     * @param integer $userID
     * @return UserInfo
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;

        return $this;
    }

    /**
     * Get userID
     *
     * @return integer 
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * Set residenceCountry
     *
     * @param string $residenceCountry
     * @return UserInfo
     */
    public function setResidenceCountry($residenceCountry)
    {
        $this->residenceCountry = $residenceCountry;

        return $this;
    }

    /**
     * Get residenceCountry
     *
     * @return string 
     */
    public function getResidenceCountry()
    {
        return $this->residenceCountry;
    }

    /**
     * Set residenceCountryVisible
     *
     * @param boolean $residenceCountryVisible
     * @return UserInfo
     */
    public function setResidenceCountryVisible($residenceCountryVisible)
    {
        $this->residenceCountryVisible = $residenceCountryVisible;

        return $this;
    }

    /**
     * Get residenceCountryVisible
     *
     * @return boolean 
     */
    public function getResidenceCountryVisible()
    {
        return $this->residenceCountryVisible;
    }

    /**
     * Set workExpirience
     *
     * @param string $workExpirience
     * @return UserInfo
     */
    public function setWorkExpirience($workExpirience)
    {
        $this->workExpirience = $workExpirience;

        return $this;
    }

    /**
     * Get workExpirience
     *
     * @return string 
     */
    public function getWorkExpirience()
    {
        return $this->workExpirience;
    }

    /**
     * Set workExpirienceVisible
     *
     * @param boolean $workExpirienceVisible
     * @return UserInfo
     */
    public function setWorkExpirienceVisible($workExpirienceVisible)
    {
        $this->workExpirienceVisible = $workExpirienceVisible;

        return $this;
    }

    /**
     * Get workExpirienceVisible
     *
     * @return boolean 
     */
    public function getWorkExpirienceVisible()
    {
        return $this->workExpirienceVisible;
    }

    /**
     * Set skills
     *
     * @param string $skills
     * @return UserInfo
     */
    public function setSkills($skills)
    {
        $this->skills = $skills;

        return $this;
    }

    /**
     * Get skills
     *
     * @return string 
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * Set skillsVisible
     *
     * @param boolean $skillsVisible
     * @return UserInfo
     */
    public function setSkillsVisible($skillsVisible)
    {
        $this->skillsVisible = $skillsVisible;

        return $this;
    }

    /**
     * Get skillsVisible
     *
     * @return boolean 
     */
    public function getSkillsVisible()
    {
        return $this->skillsVisible;
    }

    /**
     * Set interests
     *
     * @param string $interests
     * @return UserInfo
     */
    public function setInterests($interests)
    {
        $this->interests = $interests;

        return $this;
    }

    /**
     * Get interests
     *
     * @return string 
     */
    public function getInterests()
    {
        return $this->interests;
    }

    /**
     * Set interestsVisible
     *
     * @param boolean $interestsVisible
     * @return UserInfo
     */
    public function setInterestsVisible($interestsVisible)
    {
        $this->interestsVisible = $interestsVisible;

        return $this;
    }

    /**
     * Get interestsVisible
     *
     * @return boolean 
     */
    public function getInterestsVisible()
    {
        return $this->interestsVisible;
    }

    /**
     * Set volunteerWork
     *
     * @param string $volunteerWork
     * @return UserInfo
     */
    public function setVolunteerWork($volunteerWork)
    {
        $this->volunteerWork = $volunteerWork;

        return $this;
    }

    /**
     * Get volunteerWork
     *
     * @return string 
     */
    public function getVolunteerWork()
    {
        return $this->volunteerWork;
    }

    /**
     * Set volunteerWorkVisible
     *
     * @param boolean $volunteerWorkVisible
     * @return UserInfo
     */
    public function setVolunteerWorkVisible($volunteerWorkVisible)
    {
        $this->volunteerWorkVisible = $volunteerWorkVisible;

        return $this;
    }

    /**
     * Get volunteerWorkVisible
     *
     * @return boolean 
     */
    public function getVolunteerWorkVisible()
    {
        return $this->volunteerWorkVisible;
    }

    /**
     * Set societies
     *
     * @param string $societies
     * @return UserInfo
     */
    public function setSocieties($societies)
    {
        $this->societies = $societies;

        return $this;
    }

    /**
     * Get societies
     *
     * @return string 
     */
    public function getSocieties()
    {
        return $this->societies;
    }

    /**
     * Set societiesVisible
     *
     * @param boolean $societiesVisible
     * @return UserInfo
     */
    public function setSocietiesVisible($societiesVisible)
    {
        $this->societiesVisible = $societiesVisible;

        return $this;
    }

    /**
     * Get societiesVisible
     *
     * @return boolean 
     */
    public function getSocietiesVisible()
    {
        return $this->societiesVisible;
    }

    /**
     * Set facebook
     *
     * @param string $facebook
     * @return UserInfo
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return string 
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set facebookVisible
     *
     * @param boolean $facebookVisible
     * @return UserInfo
     */
    public function setFacebookVisible($facebookVisible)
    {
        $this->facebookVisible = $facebookVisible;

        return $this;
    }

    /**
     * Get facebookVisible
     *
     * @return boolean 
     */
    public function getFacebookVisible()
    {
        return $this->facebookVisible;
    }

    /**
     * Set twitter
     *
     * @param string $twitter
     * @return UserInfo
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter
     *
     * @return string 
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set twitterVisible
     *
     * @param boolean $twitterVisible
     * @return UserInfo
     */
    public function setTwitterVisible($twitterVisible)
    {
        $this->twitterVisible = $twitterVisible;

        return $this;
    }

    /**
     * Get twitterVisible
     *
     * @return boolean 
     */
    public function getTwitterVisible()
    {
        return $this->twitterVisible;
    }

    /**
     * Set linkedIn
     *
     * @param string $linkedIn
     * @return UserInfo
     */
    public function setLinkedIn($linkedIn)
    {
        $this->linkedIn = $linkedIn;

        return $this;
    }

    /**
     * Get linkedIn
     *
     * @return string 
     */
    public function getLinkedIn()
    {
        return $this->linkedIn;
    }

    /**
     * Set linkedInVisible
     *
     * @param boolean $linkedInVisible
     * @return UserInfo
     */
    public function setLinkedInVisible($linkedInVisible)
    {
        $this->linkedInVisible = $linkedInVisible;

        return $this;
    }

    /**
     * Get linkedInVisible
     *
     * @return boolean 
     */
    public function getLinkedInVisible()
    {
        return $this->linkedInVisible;
    }

    /**
     * Set googlePlus
     *
     * @param string $googlePlus
     * @return UserInfo
     */
    public function setGooglePlus($googlePlus)
    {
        $this->googlePlus = $googlePlus;

        return $this;
    }

    /**
     * Get googlePlus
     *
     * @return string 
     */
    public function getGooglePlus()
    {
        return $this->googlePlus;
    }

    /**
     * Set googlePlusVisible
     *
     * @param boolean $googlePlusVisible
     * @return UserInfo
     */
    public function setGooglePlusVisible($googlePlusVisible)
    {
        $this->googlePlusVisible = $googlePlusVisible;

        return $this;
    }

    /**
     * Get googlePlusVisible
     *
     * @return boolean 
     */
    public function getGooglePlusVisible()
    {
        return $this->googlePlusVisible;
    }

    /**
     * Set user
     *
     * @param \ZectranetBundle\Entity\User $user
     * @return UserInfo
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
