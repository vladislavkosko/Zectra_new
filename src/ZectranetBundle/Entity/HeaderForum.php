<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * HeaderForum
 *
 * @ORM\Table(name="header_forums")
 * @ORM\Entity
 */
class HeaderForum
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
     * @ORM\Column(name="onwer_id", type="integer")
     */
    private $ownerID;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="onwer_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Header", mappedBy="forum", cascade={"remove"})
     */
    private $headers;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var boolean
     * @ORM\Column(name="shared", type="boolean", options={"default" = false})
     */
    private $shared;

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * Constructor
     */
    public function __construct() {
        $this->created = new \DateTime();
        $this->shared = false;
    }

    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'ownerID' => $this->getOwnerID(),
            'shared' => $this->getShared(),
            'headers' => EntityOperations::arrayToJsonArray($this->getHeaders()),
        );
    }

    /**
     * @param EntityManager $em
     * @param int $user_id
     * @param array $params
     * @return HeaderForum
     */
    public static function addNewHeaderForum(EntityManager $em, $user_id, $params) {
        $user = $em->getRepository('ZectranetBundle:User')->find($user_id);
        $project = new HeaderForum();
        $project->setOwner($user);
        $project->setName($params['name']);
        $em->persist($project);
        $em->flush();

        return $project;
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
     * Set ownerID
     *
     * @param integer $ownerID
     * @return HeaderForum
     */
    public function setOwnerID($ownerID)
    {
        $this->ownerID = $ownerID;

        return $this;
    }

    /**
     * Get ownerID
     *
     * @return integer 
     */
    public function getOwnerID()
    {
        return $this->ownerID;
    }

    /**
     * Set shared
     *
     * @param boolean $shared
     * @return HeaderForum
     */
    public function setShared($shared)
    {
        $this->shared = $shared;

        return $this;
    }

    /**
     * Get shared
     *
     * @return boolean 
     */
    public function getShared()
    {
        return $this->shared;
    }

    /**
     * Set owner
     *
     * @param \ZectranetBundle\Entity\User $owner
     * @return HeaderForum
     */
    public function setOwner(\ZectranetBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \ZectranetBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return HeaderForum
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return HeaderForum
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Add headers
     *
     * @param \ZectranetBundle\Entity\Header $headers
     * @return HeaderForum
     */
    public function addHeader(\ZectranetBundle\Entity\Header $headers)
    {
        $this->headers[] = $headers;

        return $this;
    }

    /**
     * Remove headers
     *
     * @param \ZectranetBundle\Entity\Header $headers
     */
    public function removeHeader(\ZectranetBundle\Entity\Header $headers)
    {
        $this->headers->removeElement($headers);
    }

    /**
     * Get headers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
