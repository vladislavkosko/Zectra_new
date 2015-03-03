<?php

namespace ZectranetBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Role
 *
 * @ORM\Table(name="roles")
 * @ORM\Entity
 */
class Role implements RoleInterface
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getRole()
    {
        return $this->getName();
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
     * Set name
     *
     * @param string $name
     * @return Role
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
     * @param EntityManager $em
     * @return Role
     */
    public static function getAdminRole(EntityManager $em)
    {
        return $em->getRepository("ZectranetBundle:Role")->find(1);
    }

    /**
     * @param EntityManager $em
     * @return Role
     */
    public static function getUserRole(EntityManager $em)
    {
        return $em->getRepository("ZectranetBundle:Role")->find(2);
    }
}
