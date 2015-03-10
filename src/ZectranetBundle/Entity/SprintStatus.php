<?php
namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * SprintStatus
 *
 * @ORM\Table(name="sprint_statuses")
 * @ORM\Entity
 */
class SprintStatus
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
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @var string
     * @ORM\Column(name="color", type="string", length=255)
     */
    private $color;

    /**
     * @ORM\OneToMany(targetEntity="Sprint", mappedBy="status")
     * @var array
     */
    private $sprint;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sprint = new ArrayCollection();
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
     * Set label
     *
     * @param string $label
     * @return SprintStatus
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set color
     *
     * @param string $color
     * @return SprintStatus
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Add sprint
     *
     * @param \ZectranetBundle\Entity\Sprint $sprint
     * @return SprintStatus
     */
    public function addSprint(Sprint $sprint)
    {
        $this->sprint[] = $sprint;

        return $this;
    }

    /**
     * Remove sprint
     *
     * @param \ZectranetBundle\Entity\Sprint $sprint
     */
    public function removeSprint(Sprint $sprint)
    {
        $this->sprint->removeElement($sprint);
    }

    /**
     * Get sprint
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSprint()
    {
        return $this->sprint;
    }

    /**
     * @param EntityManager $em
     * @return SprintStatus
     */
    public static function getOpenStatus(EntityManager $em) {
        return $em->getRepository('ZectranetBundle:SprintStatus')->find(1);
    }

    /**
     * @param EntityManager $em
     * @return SprintStatus
     */
    public static function getInProgressStatus(EntityManager $em) {
        return $em->getRepository('ZectranetBundle:SprintStatus')->find(2);
    }

    /**
     * @param EntityManager $em
     * @return SprintStatus
     */
    public static function getClosedStatus(EntityManager $em) {
        return $em->getRepository('ZectranetBundle:SprintStatus')->find(3);
    }

    /**
     * @return array
     */
    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'color' => $this->getColor(),
            'label' => $this->getLabel(),
        );
    }
}
