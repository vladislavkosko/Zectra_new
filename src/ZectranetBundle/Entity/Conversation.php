<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Conversation
 *
 * @ORM\Table(name="conversations")
 * @ORM\Entity
 */
class Conversation
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
     * @var int
     * @ORM\Column(name="user1_id", type="integer")
     */
    private $user1ID;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user1_id", referencedColumnName="id")
     */
    private $user1;

    /**
     * @var int
     * @ORM\Column(name="user2_id", type="integer")
     */
    private $user2ID;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user2_id", referencedColumnName="id")
     */
    private $user2;

    /**
     * @var int
     * @ORM\Column(name="office_id", type="integer")
     */
    private $officeID;

    /**
     * @var Office
     * @ORM\ManyToOne(targetEntity="Office")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ConversationMessage", mappedBy="conversation")
     */
    private $messages;

    public function __construct() {
        $this->messages = new ArrayCollection();
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
     * Set user1ID
     *
     * @param integer $user1ID
     * @return Conversation
     */
    public function setUser1ID($user1ID)
    {
        $this->user1ID = $user1ID;

        return $this;
    }

    /**
     * Get user1ID
     *
     * @return integer 
     */
    public function getUser1ID()
    {
        return $this->user1ID;
    }

    /**
     * Set user2ID
     *
     * @param integer $user2ID
     * @return Conversation
     */
    public function setUser2ID($user2ID)
    {
        $this->user2ID = $user2ID;

        return $this;
    }

    /**
     * Get user2ID
     *
     * @return integer 
     */
    public function getUser2ID()
    {
        return $this->user2ID;
    }

    /**
     * Set officeID
     *
     * @param integer $officeID
     * @return Conversation
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
     * Set user1
     *
     * @param \ZectranetBundle\Entity\User $user1
     * @return Conversation
     */
    public function setUser1(\ZectranetBundle\Entity\User $user1 = null)
    {
        $this->user1 = $user1;

        return $this;
    }

    /**
     * Get user1
     *
     * @return \ZectranetBundle\Entity\User 
     */
    public function getUser1()
    {
        return $this->user1;
    }

    /**
     * Set user2
     *
     * @param \ZectranetBundle\Entity\User $user2
     * @return Conversation
     */
    public function setUser2(\ZectranetBundle\Entity\User $user2 = null)
    {
        $this->user2 = $user2;

        return $this;
    }

    /**
     * Get user2
     *
     * @return \ZectranetBundle\Entity\User 
     */
    public function getUser2()
    {
        return $this->user2;
    }

    /**
     * Set office
     *
     * @param \ZectranetBundle\Entity\Office $office
     * @return Conversation
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
     * Add messages
     *
     * @param \ZectranetBundle\Entity\ConversationMessage $messages
     * @return Conversation
     */
    public function addMessage(\ZectranetBundle\Entity\ConversationMessage $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param \ZectranetBundle\Entity\ConversationMessage $messages
     */
    public function removeMessage(\ZectranetBundle\Entity\ConversationMessage $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
