<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ErrorLog
 *
 * @ORM\Table(name="error_logs")
 * @ORM\Entity
 */
class ErrorLog
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
     * @ORM\Column(name="message", type="string", length=10000)
     */
    private $message;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(name="where_error", type="string", length=255)
     */
    private $whereError;

    /**
     * @var string
     * @ORM\Column(name="from_error", type="string", length=255)
     */
    private $fromError;

    public function getInArray() {
        return array(
            'id' => $this->getId(),
            'message' => $this->getMessage(),
            'date' => $this->getDate()->format('Y-m-d H:i:s'),
            'where' => $this->getWhereError(),
            'from' => $this->getFromError(),
        );
    }

    public function __construct() {
        $this->setDate(new \DateTime());
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
     * Set message
     *
     * @param string $message
     * @return ErrorLog
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return ErrorLog
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set whereError
     *
     * @param string $whereError
     * @return ErrorLog
     */
    public function setWhereError($whereError)
    {
        $this->whereError = $whereError;

        return $this;
    }

    /**
     * Get whereError
     *
     * @return string 
     */
    public function getWhereError()
    {
        return $this->whereError;
    }

    /**
     * Set fromError
     *
     * @param string $fromError
     * @return ErrorLog
     */
    public function setFromError($fromError)
    {
        $this->fromError = $fromError;

        return $this;
    }

    /**
     * Get fromError
     *
     * @return string 
     */
    public function getFromError()
    {
        return $this->fromError;
    }
}
