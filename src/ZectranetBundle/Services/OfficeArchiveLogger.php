<?php

namespace ZectranetBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Security;
use ZectranetBundle\Entity\Office;
use ZectranetBundle\Entity\OfficeArchiveLog;
use ZectranetBundle\Entity\User;

class OfficeArchiveLogger
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
     * @param Office $office
     */
    public function logEvent($eventMsg, $office) {
        $event = new OfficeArchiveLog();
        $event->setMessage($eventMsg);
        $event->setOffice($office);
        $this->em->persist($event);
        $this->em->flush();
    }
}