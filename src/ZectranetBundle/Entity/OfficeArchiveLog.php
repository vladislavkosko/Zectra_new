<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * OfficeArchiveLog
 *
 * @ORM\Table(name="office_archive_logs")
 * @ORM\Entity
 */
class OfficeArchiveLog
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
     * @var int
     * @ORM\Column(name="office_id", type="integer")
     */
    private $officeID;

    /**
     * @var Office
     * @ORM\ManyToOne(targetEntity="Office", inversedBy="logs")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @return array
     */
    public function getInArray()
    {
        return array(
            'id' => $this->getId(),
            'date' => $this->getDate()->format('Y-m-d H:i:s'),
            'event' => $this->getMessage(),
        );
    }

    public static function getArchiveLogs(EntityManager $em, $office_id) {
        return EntityOperations::arrayToJsonArray(
            $em->getRepository('ZectranetBundle:OfficeArchiveLog')->findBy(
                array('officeID' => $office_id), array('id' => 'DESC'))
        );
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->date = new \DateTime();
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
     * @return OfficeArchiveLog
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
     * Set officeID
     *
     * @param integer $officeID
     * @return OfficeArchiveLog
     */
    public function setOfficeID($officeID)
    {
        $this->officeID = $officeID;

        return $this;
    }

    /**
     * Get officeID
     *
     * @return integer 
     */
    public function getOfficeID()
    {
        return $this->officeID;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return OfficeArchiveLog
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
     * Set office
     *
     * @param \ZectranetBundle\Entity\Office $office
     * @return OfficeArchiveLog
     */
    public function setOffice(\ZectranetBundle\Entity\Office $office = null)
    {
        $this->office = $office;

        return $this;
    }

    /**
     * Get office
     *
     * @return \ZectranetBundle\Entity\Office 
     */
    public function getOffice()
    {
        return $this->office;
    }
}
