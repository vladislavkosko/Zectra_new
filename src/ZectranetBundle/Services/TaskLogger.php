<?php

namespace ZectranetBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Security;
use ZectranetBundle\Entity\Notification;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\Office;
use ZectranetBundle\Entity\TaskLog;
use ZectranetBundle\Entity\User;
use Symfony\Component\Debug\Exception\FatalErrorException;


class TaskLogger
{
    /** @var User $user */
    private $user;
    /** @var EntityManager $em */
    private $em;
    /** @var array $types */
    private $types;

    /**
     * @param TokenStorage $tokenStorage
     * @param EntityManager $em
     */
    public function __construct(TokenStorage $tokenStorage, EntityManager $em)
    {
        $this->user = $tokenStorage->getToken()->getUser();
        $this->em = $em;
        $this->types = array(
            0 => 'Name Changed',
            1 => 'Description Changed',
            2 => 'Type Changed',
            3 => 'Priority Changed',
            4 => 'Status Changed',
            5 => 'Project/Epic Story Changed',
            6 => 'Assigned Changed',
            7 => 'Progress Changed',
            8 => 'Estimated Hours Changed',
            9 => 'Estimated Minutes Changed',
            10 => 'Start Date Changed',
            11 => 'End Date Changed',
            12 => 'Version Changed',
        );
    }

    /**
     * @param int $type
     * @param int $task_id
     * @param string $valueBefore
     * @param string $valueAfter
     */
    public function valueChanged($type, $task_id, $valueBefore, $valueAfter) {
        $task = $this->em->getRepository('ZectranetBundle:Task')->find($task_id);
        $taskLog = new TaskLog();
        $taskLog->setUser($this->user);
        $taskLog->setTask($task);
        $taskLog->setValueBefore($valueBefore);
        $taskLog->setValueAfter($valueAfter);
        $taskLog->setType($this->types[$type]);

        $this->em->persist($taskLog);
        $this->em->flush();
    }
}