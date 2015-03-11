<?php

namespace ZectranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RequestType
 *
 * @ORM\Table(name="request_types")
 * @ORM\Entity
 */
class RequestType
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
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @var string
     * @ORM\Column(name="color", type="string", length=255)
     */
    private $color;

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
     * @return RequestType
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
     * @return RequestType
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
}
