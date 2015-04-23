<?php

namespace ZectranetBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Security;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\Office;
use ZectranetBundle\Entity\OfficeLog;
use ZectranetBundle\Entity\User;

class OfficeLogger
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
        /****/
        $event->setMessage($eventMsg);
        $this->em->persist($event);
        $this->em->flush();
    }
}