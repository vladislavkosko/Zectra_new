<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Office
 *
 * @ORM\Table(name="offices")
 * @ORM\Entity
 */
class Office
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

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="owner_id", type="integer")
     */
    private $ownerid;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ownedOffices")
     * @ORM\JoinColumn(name="owner_id")
     * @var User
     */
    private $owner;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="assignedOffices", fetch="EXTRA_LAZY")
     * @var ArrayCollection
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="Project", mappedBy="offices", fetch="EXTRA_LAZY")
     * @var ArrayCollection
     */
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity="Sprint", mappedBy="office", cascade={"remove"})
     * @var ArrayCollection
     */
    private $sprints;

    /**
     * @ORM\OneToMany(targetEntity="OfficePost", mappedBy="office", cascade={"remove"})
     * @ORM\OrderBy({"posted" = "DESC"})
     * @var ArrayCollection
     */
    private $postsOffice;

    /**
     * @ORM\OneToMany(targetEntity="OfficeRole", mappedBy="office", cascade={"remove"})
     * @var ArrayCollection
     */
    private $officeUserRoles;

    /**
     * @var bool $visible
     * @ORM\Column(name="visible", type="boolean", options={"default" = false})
     */
    private $visible;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->visible = false;
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
     * @return Office
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
     * Set description
     *
     * @param string $description
     * @return Office
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set ownerid
     *
     * @param integer $ownerid
     * @return Office
     */
    public function setOwnerid($ownerid)
    {
        $this->ownerid = $ownerid;

        return $this;
    }

    /**
     * Get ownerid
     *
     * @return integer 
     */
    public function getOwnerid()
    {
        return $this->ownerid;
    }

    /**
     * Set owner
     *
     * @param \ZectranetBundle\Entity\User $owner
     * @return Office
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
     * Add users
     *
     * @param \ZectranetBundle\Entity\User $users
     * @return Office
     */
    public function addUser(\ZectranetBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \ZectranetBundle\Entity\User $users
     */
    public function removeUser(\ZectranetBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add projects
     *
     * @param \ZectranetBundle\Entity\Project $projects
     * @return Office
     */
    public function addProject(\ZectranetBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;

        return $this;
    }

    /**
     * Remove projects
     *
     * @param \ZectranetBundle\Entity\Project $projects
     */
    public function removeProject(\ZectranetBundle\Entity\Project $projects)
    {
        $this->projects->removeElement($projects);
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Add postsOffice
     *
     * @param \ZectranetBundle\Entity\OfficePost $postsOffice
     * @return Office
     */
    public function addPostsOffice(\ZectranetBundle\Entity\OfficePost $postsOffice)
    {
        $this->postsOffice[] = $postsOffice;

        return $this;
    }

    /**
     * Remove postsOffice
     *
     * @param \ZectranetBundle\Entity\OfficePost $postsOffice
     */
    public function removePostsOffice(\ZectranetBundle\Entity\OfficePost $postsOffice)
    {
        $this->postsOffice->removeElement($postsOffice);
    }

    /**
     * Get postsOffice
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPostsOffice()
    {
        return $this->postsOffice;
    }

    /**
     * Add officeUserRoles
     *
     * @param \ZectranetBundle\Entity\OfficeRole $officeUserRoles
     * @return Office
     */
    public function addOfficeUserRole(\ZectranetBundle\Entity\OfficeRole $officeUserRoles)
    {
        $this->officeUserRoles[] = $officeUserRoles;

        return $this;
    }

    /**
     * Remove officeUserRoles
     *
     * @param \ZectranetBundle\Entity\OfficeRole $officeUserRoles
     */
    public function removeOfficeUserRole(\ZectranetBundle\Entity\OfficeRole $officeUserRoles)
    {
        $this->officeUserRoles->removeElement($officeUserRoles);
    }

    /**
     * Get officeUserRoles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOfficeUserRoles()
    {
        return $this->officeUserRoles;
    }

    /**
     * Add sprints
     *
     * @param \ZectranetBundle\Entity\Sprint $sprints
     * @return Office
     */
    public function addSprint(\ZectranetBundle\Entity\Sprint $sprints)
    {
        $this->sprints[] = $sprints;

        return $this;
    }

    /**
     * Remove sprints
     *
     * @param \ZectranetBundle\Entity\Sprint $sprints
     */
    public function removeSprint(\ZectranetBundle\Entity\Sprint $sprints)
    {
        $this->sprints->removeElement($sprints);
    }

    /**
     * Get sprints
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSprints()
    {
        return $this->sprints;
    }

    /**
     * @param EntityManager $em
     * @param User $user
     * @param $name
     * @param $description
     * @return Office
     */
    public static function addNewOffice(EntityManager $em, User $user, $name, $description) {
        $office = new Office();
        $office->setOwner($user);
        $office->setName($name);
        $office->setDescription($description);
        $em->persist($office);
        $em->flush();

        return $office;
    }

    /**
     * @param EntityManager $em
     * @param int $office_id
     */
    public static function deleteOffice(EntityManager $em, $office_id) {
        /** @var Office $office */
        $office = $em->getRepository('ZectranetBundle:Office')->find($office_id);

        /** @var OfficeRole $role */
        foreach ($office->getOfficeUserRoles() as $role) {
            $office->removeOfficeUserRole($role);
        }

        /** @var OfficePost $post */
        foreach ($office->getPostsOffice() as $post) {
            $office->removePostsOffice($post);
        }

        /** @var Project $project */
        foreach ($office->getProjects() as $project) {
            $office->removeProject($project);
        }

        /** @var User $user */
        foreach ($office->getUsers() as $user) {
            $office->removeUser($user);
        }

        $em->remove($office);
        $em->flush();
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return Office
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'description' => $this->getDescription(),
            'name' => $this->getName(),
            'owner' => $this->getOwner()->getInArray(),
            'visible' => $this->getVisible(),
        );
    }
}
