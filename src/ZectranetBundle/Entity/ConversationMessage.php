<?php

namespace ZectranetBundle\Entity;

use Doctrine\Entity;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * ConversationMessage
 *
 * @ORM\Table(name="conversation_messages")
 * @ORM\Entity
 */
class ConversationMessage
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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userID;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var int
     * @ORM\Column(name="conversation_id", type="integer")
     */
    private $conversationID;

    /**
     * @var Conversation
     * @ORM\ManyToOne(targetEntity="Conversation", inversedBy="messages")
     * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id")
     */
    private $conversation;

    /**
     * @var string
     * @ORM\Column(name="message", type="string", length=3000)
     */
    private $message;

    /**
     * @var \DateTime
     * @ORM\Column(name="posted", type="datetime")
     */
    private $posted;

    /**
     * @var \DateTime
     * @ORM\Column(name="edited", type="datetime", nullable=true, options={"default" = null})
     */
    private $edited;

    public function __construct() {
        $this->posted = new \DateTime();
    }

    /**
     * @param EntityManager $em
     * @param int $conversation_id
     * @param int $user_id
     * @param string $message
     * @return ConversationMessage
     */
    public static function addNewMessage(EntityManager $em, $conversation_id, $user_id, $message) {
        $conversation = $em->find('ZectranetBundle:Conversation', $conversation_id);
        $user = $em->find('ZectranetBundle:User', $user_id);
        $convMessage = new ConversationMessage();
        $convMessage->setMessage($message);
        $convMessage->setConversation($conversation);
        $convMessage->setUser($user);
        $em->persist($convMessage);
        $em->flush();
        return $convMessage;
    }

    public static function editMessage(EntityManager $em, $message_id, $message) {

        $post = $em->getRepository('ZectranetBundle:ConversationMessage')->find($message_id);

        $post->setEdited(new \DateTime());
        $post->setMessage($message);

        $em->persist($post);
        $em->flush();
        return $post;
    }

    /**
     * @return array
     */
    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'user' => $this->getUser()->getInArray(),
            'conversationID' => $this->getConversationID(),
            'message' => $this->getMessage(),
            'posted' => $this->getPosted()->format('Y-m-d H:i:s'),
            'edited' => ($this->getEdited() != null) ? $this->getEdited()->format('Y-m-d H:i:s') : null,
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
     * Set userID
     *
     * @param integer $userID
     * @return ConversationMessage
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
     * Set conversationID
     *
     * @param string $conversationID
     * @return ConversationMessage
     */
    public function setConversationID($conversationID)
    {
        $this->conversationID = $conversationID;

        return $this;
    }

    /**
     * Get conversationID
     *
     * @return string 
     */
    public function getConversationID()
    {
        return $this->conversationID;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return ConversationMessage
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
     * Set posted
     *
     * @param \DateTime $posted
     * @return ConversationMessage
     */
    public function setPosted($posted)
    {
        $this->posted = $posted;

        return $this;
    }

    /**
     * Get posted
     *
     * @return \DateTime 
     */
    public function getPosted()
    {
        return $this->posted;
    }

    /**
     * Set edited
     *
     * @param \DateTime $edited
     * @return ConversationMessage
     */
    public function setEdited($edited)
    {
        $this->edited = $edited;

        return $this;
    }

    /**
     * Get edited
     *
     * @return \DateTime 
     */
    public function getEdited()
    {
        return $this->edited;
    }

    /**
     * Set user
     *
     * @param \ZectranetBundle\Entity\User $user
     * @return ConversationMessage
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
     * Set conversation
     *
     * @param \ZectranetBundle\Entity\Conversation $conversation
     * @return ConversationMessage
     */
    public function setConversation(\ZectranetBundle\Entity\Conversation $conversation = null)
    {
        $this->conversation = $conversation;

        return $this;
    }

    /**
     * Get conversation
     *
     * @return \ZectranetBundle\Entity\Conversation 
     */
    public function getConversation()
    {
        return $this->conversation;
    }
}
