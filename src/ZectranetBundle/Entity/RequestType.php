<?php

namespace ZectranetBundle\Entity;

use Doctrine\ORM\EntityManager;
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

    /**
     * @param EntityManager $em
     * @return RequestType
     */
    public static function getContactMembershipRequest(EntityManager $em) {
        return $em->find('ZectranetBundle:RequestType', 5);
    }

    /**
     * @param EntityManager $em
     * @return RequestType
     */
    public static function getContactMembershipRequestBack(EntityManager $em) {
        return $em->find('ZectranetBundle:RequestType', 6);
    }

    /**
     * @param EntityManager $em
     * @return RequestType
     */
    public static function getContactMembershipRequestResend(EntityManager $em) {
        return $em->find('ZectranetBundle:RequestType', 7);
    }

    /**
     * @param EntityManager $em
     * @return RequestType
     */
    public static function getProjectMembershipRequest(EntityManager $em) {
        return $em->find('ZectranetBundle:RequestType', 8);
    }

    /**
     * @param EntityManager $em
     * @return RequestType
     */
    public static function getProjectMembershipRequestBack(EntityManager $em) {
        return $em->find('ZectranetBundle:RequestType', 9);
    }

    /**
     * @param EntityManager $em
     * @return RequestType
     */
    public static function getProjectMembershipRequestResend(EntityManager $em) {
        return $em->find('ZectranetBundle:RequestType', 10);
    }

    /**
     * @param EntityManager $em
     * @return RequestType
     */
    public static function getQnAMembershipRequest(EntityManager $em) {
        return $em->find('ZectranetBundle:RequestType', 13);
    }

    /**
     * @param EntityManager $em
     * @return RequestType
     */
    public static function getQnAMembershipRequestBack(EntityManager $em) {
        return $em->find('ZectranetBundle:RequestType', 14);
    }

    /**
     * @param EntityManager $em
     * @return RequestType
     */
    public static function getQnAMembershipRequestResend(EntityManager $em) {
        return $em->find('ZectranetBundle:RequestType', 15);
    }

    /**
     * @param EntityManager $em
     * @return RequestType
     */
    public static function getDevelopmentMembershipRequest(EntityManager $em) {
        return $em->find('ZectranetBundle:RequestType', 16);
    }

    /**
     * @param EntityManager $em
     * @return RequestType
     */
    public static function getDevelopmentMembershipRequestBack(EntityManager $em) {
        return $em->find('ZectranetBundle:RequestType', 17);
    }

    /**
     * @param EntityManager $em
     * @return RequestType
     */
    public static function getDevelopmentMembershipRequestResend(EntityManager $em) {
        return $em->find('ZectranetBundle:RequestType', 18);
    }
}
