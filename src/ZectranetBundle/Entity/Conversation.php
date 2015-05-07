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
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ConversationMessage", mappedBy="conversation")
     */
    private $messages;

    public function __construct() {
        $this->messages = new ArrayCollection();
    }

    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'messages' => EntityOperations::arrayToJsonArray($this->getMessages()),
            'user1' => $this->getUser1()->getInArray(),
            'user2' => $this->getUser2()->getInArray(),
        );
    }

    /**
     * @param EntityManager $em
     * @param int $user_id
     * @param int $contact_id
     */
    public static function addConversation(EntityManager $em, $user_id, $contact_id) {
        $user = $em->find('ZectranetBundle:User', $user_id);
        $contact = $em->find('ZectranetBundle:User', $contact_id);
        $conversation = new Conversation();
        $conversation->setUser1($user);
        $conversation->setUser2($contact);
        $em->persist($conversation);
        $em->flush();
    }

    /**
     * @param EntityManager $em
     * @param int $user1
     * @param int $user2
     * @return mixed
     */
    public static function getConversation(EntityManager $em, $user1, $user2) {
        $qb = $em->createQueryBuilder();
        $query = $qb->select('c')
            ->from('ZectranetBundle:Conversation', 'c')
            ->where('(c.user1ID = :user1 AND c.user2ID = :user2) OR (c.user1ID = :user2 AND c.user2ID = :user1)')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->getQuery();
        $conversation = null;
        try {
            $conversation = $query->getSingleResult();
        } catch (\Exception $ex) {
            $conversation = null;
        }
        return $conversation;
    }

    /**
     * @param EntityManager $em
     * @param int $userID
     * @return mixed
     */
    public static function getConversationByUser(EntityManager $em, $userID) {
        $qb = $em->createQueryBuilder();
        $query = $qb->select('c')
            ->from('ZectranetBundle:Conversation', 'c')
            ->where('c.user1ID = :user OR c.user2ID = :user')
            ->setParameter('user', $userID)
            ->getQuery();
        $conversations = null;
        try {
            $conversations = $query->getResult();
        } catch (\Exception $ex) {
            $conversation = null;
        }
        return $conversations;
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
