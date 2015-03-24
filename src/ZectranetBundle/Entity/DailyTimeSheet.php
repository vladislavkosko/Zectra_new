<?php

namespace ZectranetBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * DailyTimeSheet
 *
 * @ORM\Table(name="daily_time_sheet")
 * @ORM\Entity
 */
class DailyTimeSheet
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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userid;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="wde")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true, options={"default":NULL})
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="time", nullable=true, options={"default":NULL})
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="time", nullable=true, options={"default":NULL})
     */
    private $endTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="begin_lunch", type="time", nullable=true, options={"default":NULL})
     */
    private $beginLunch;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_lunch", type="time", nullable=true, options={"default":NULL})
     */
    private $endLunch;

    /**
     * @var float
     *
     * @ORM\Column(name="hours", type="float", nullable=true, options={"default":NULL})
     */
    private $hours;

    /**
     * @var string
     *
     * @ORM\Column(name="main_task", type="string", nullable=true, options={"default":NULL})
     */
    private $mainTask;

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
     * @return DailyTimeSheet
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
     * Set date
     *
     * @param \DateTime $date
     * @return DailyTimeSheet
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
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return DailyTimeSheet
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return DailyTimeSheet
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set beginLunch
     *
     * @param \DateTime $beginLunch
     * @return DailyTimeSheet
     */
    public function setBeginLunch($beginLunch)
    {
        $this->beginLunch = $beginLunch;

        return $this;
    }

    /**
     * Get beginLunch
     *
     * @return \DateTime 
     */
    public function getBeginLunch()
    {
        return $this->beginLunch;
    }

    /**
     * Set endLunch
     *
     * @param \DateTime $endLunch
     * @return DailyTimeSheet
     */
    public function setEndLunch($endLunch)
    {
        $this->endLunch = $endLunch;

        return $this;
    }

    /**
     * Get endLunch
     *
     * @return \DateTime 
     */
    public function getEndLunch()
    {
        return $this->endLunch;
    }

    /**
     * Set hours
     *
     * @param float $hours
     * @return DailyTimeSheet
     */
    public function setHours($hours)
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * Get hours
     *
     * @return float 
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * Set mainTask
     *
     * @param string $mainTask
     * @return DailyTimeSheet
     */
    public function setMainTask($mainTask)
    {
        $this->mainTask = $mainTask;

        return $this;
    }

    /**
     * Get mainTask
     *
     * @return string 
     */
    public function getMainTask()
    {
        return $this->mainTask;
    }

    /**
     * Set user
     *
     * @param \ZectranetBundle\Entity\User $user
     * @return DailyTimeSheet
     */
    public function setUser(\ZectranetBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \ZectranetBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getInArray()
    {
        return array(
            'id' => $this->getId(),
            'userid' => $this->getUserid(),
            'user' => $this->getUser()->getInArray(),
            'date' => $this->getDate()->format('Y-m-d'),
            'startOffice' => ($this->getStartTime() != null) ? $this->getStartTime()->format('H:i'): '-',
            'startLunch' => ($this->getBeginLunch() != null) ? $this->getBeginLunch()->format('H:i'): '-',
            'endLunch' => ($this->getEndLunch() != null) ? $this->getEndLunch()->format('H:i'): '-',
            'endOffice' => ($this->getEndTime() != null) ? $this->getEndTime()->format('H:i'): '-',
            'hours' => ($this->getHours() != null) ? $this->getHours(): '-',
            'mainTask' => ($this->getMainTask() != null) ? $this->getMainTask(): '-'
        );
    }

    /**
     * @param EntityManager $em
     * @param User $user
     * @param $parameters
     */
    public static function createWDE($em, $user, $parameters)
    {
        $newWDE = new DailyTimeSheet();
        $newWDE->setUser($user);
        $newWDE->setDate(new \DateTime());

        $newWDE->setStartTime(($parameters['startOffice']) ? date_create_from_format('H:i', $parameters['startOffice']) : null);
        $newWDE->setBeginLunch(($parameters['startLunch']) ? date_create_from_format('H:i', $parameters['startLunch']) : null);
        $newWDE->setEndLunch(($parameters['endLunch']) ? date_create_from_format('H:i', $parameters['endLunch']) : null);
        $newWDE->setEndTime(($parameters['endOffice']) ? date_create_from_format('H:i', $parameters['endOffice']) : null);
        $newWDE->setHours(($parameters['hours']) ? $parameters['hours'] : null);
        $newWDE->setMainTask(($parameters['mainTask']) ? $parameters['mainTask'] : null);

        $em->persist($newWDE);
        $em->flush();
    }

    /**
     * @param EntityManager $em
     * @param User $user
     * @param $parameters
     * @param $currentDate
     */
    public static  function updateWDE($em, $user, $parameters, $currentDate)
    {
        $currentWDE = $em->getRepository('ZectranetBundle:DailyTimeSheet')->findOneBy(array('date' => $currentDate, 'userid' => $user->getId()));

        $currentWDE->setStartTime(($parameters['startOffice']) ? date_create_from_format('H:i', $parameters['startOffice']) : null);
        $currentWDE->setBeginLunch(($parameters['startLunch']) ? date_create_from_format('H:i', $parameters['startLunch']) : null);
        $currentWDE->setEndLunch(($parameters['endLunch']) ? date_create_from_format('H:i', $parameters['endLunch']) : null);
        $currentWDE->setEndTime(($parameters['endOffice']) ? date_create_from_format('H:i', $parameters['endOffice']) : null);
        $currentWDE->setHours(($parameters['hours']) ? $parameters['hours'] : null);
        $currentWDE->setMainTask(($parameters['mainTask']) ? $parameters['mainTask'] : null);

        $em->flush();
    }
}
