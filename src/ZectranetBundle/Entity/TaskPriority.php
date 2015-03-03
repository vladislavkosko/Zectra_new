<?php

namespace ZectranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskStatus
 *
 * @ORM\Table(name="task_priority")
 * @ORM\Entity
 */
class TaskPriority
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="label", type="string", length=50)
     */
    private $label;

    /**
     * @var string
     * @ORM\Column(name="color", type="string", length=50)
     */
    private $color;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="priority")
     * @var array
     */
    private $tasks;

    /**
     * @return array
     */
    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'label' => $this->getLabel(),
            'color' => $this->getColor(),
        );
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return TaskPriority
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
     * @return TaskPriority
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
     * Add tasks
     *
     * @param \ZectranetBundle\Entity\Task $tasks
     * @return TaskPriority
     */
    public function addTask(\ZectranetBundle\Entity\Task $tasks)
    {
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove tasks
     *
     * @param \ZectranetBundle\Entity\Task $tasks
     */
    public function removeTask(\ZectranetBundle\Entity\Task $tasks)
    {
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTasks()
    {
        return $this->tasks;
    }
}
