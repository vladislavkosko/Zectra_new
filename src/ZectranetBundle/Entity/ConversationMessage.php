<?php

namespace ZectranetBundle\Entity;

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
    private $userID;
    private $user;
    private $conversationID;
    private $conversation;
    private $message;
    private $posted;
    private $edited;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
