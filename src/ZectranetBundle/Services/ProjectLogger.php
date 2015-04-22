<?php

namespace ZectranetBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Security;
use ZectranetBundle\Entity\HFLog;
use ZectranetBundle\Entity\Notification;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\Office;
use ZectranetBundle\Entity\ProjectLog;
use ZectranetBundle\Entity\QnALog;
use ZectranetBundle\Entity\TaskLog;
use ZectranetBundle\Entity\User;

class ProjectLogger
{
    /** @var User $user */
    private $user;
    /** @var EntityManager $em */
    private $em;

    /**
     * @param TokenStorage $tokenStorage
     * @param EntityManager $em
     */
    public function __construct(TokenStorage $tokenStorage, EntityManager $em) {
        $this->user = $tokenStorage->getToken()->getUser();
        $this->em = $em;
    }

    /**
     * @param string $eventMsg
     * @param int $projectID
     * @param int $projectTypeID
     */
    public function logEvent($eventMsg = null, $projectID, $projectTypeID) {
        $event = null;
        switch ($projectTypeID) {
            case 1:
                $event = new QnALog();
                $project = $this->em->find('ZectranetBundle:QnAForum', $projectID);
                $event->setProject($project);
                break;
            case 2:
                $event = new HFLog();
                $project = $this->em->find('ZectranetBundle:HFForum', $projectID);
                $event->setProject($project);
                break;
            case 3:
                break;
            case 4:
                $event = new ProjectLog();
                $project = $this->em->find('ZectranetBundle:Project', $projectID);
                $event->setProject($project);
                break;
        }
        $event->setMessage($eventMsg);
        $this->em->persist($event);
        $this->em->flush();
    }
}