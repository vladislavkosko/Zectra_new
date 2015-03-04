<?php

namespace ZectranetBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use ZectranetBundle\Entity\User;

/**
 * ForgotPassword
 *
 * @ORM\Table(name="forgot_password")
 * @ORM\Entity
 */
class ForgotPassword
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
     * @var integer
     *
     * @ORM\Column(name="userid", type="integer")
     */
    private $userid;

    /**
     * @var string
     *
     * @ORM\Column(name="keyForAccess", type="text")
     */
    private $keyForAccess;

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
     * Set userid
     *
     * @param integer $userid
     * @return ForgotPassword
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return integer 
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Set keyForAccess
     *
     * @param string $keyForAccess
     * @return ForgotPassword
     */
    public function setKeyForAccess($keyForAccess)
    {
        $this->keyForAccess = $keyForAccess;

        return $this;
    }

    /**
     * Get keyForAccess
     *
     * @return string 
     */
    public function getKeyForAccess()
    {
        return $this->keyForAccess;
    }

    /**
     * @param EntityManager $em
     * @param User $user
     * @return string
     */
    public static function addForgotRecord($em, $user)
    {
        $newRecord = new ForgotPassword();

        $datetime = new \DateTime();
        srand($datetime->format('s'));
        $keyForAccess = md5(rand(1000, 100000));

        $newRecord->setUserid($user->getId());
        $newRecord->setKeyForAccess($keyForAccess);
        $em->persist($newRecord);
        $em->flush();

        return $keyForAccess;
    }
}
