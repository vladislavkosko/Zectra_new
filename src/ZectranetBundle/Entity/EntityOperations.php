<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;

class EntityOperations {
    /**
     * @param array|ArrayCollection|Collection $array
     * @return array
     */
    public static function arrayToJsonArray($array) {
        $jsonArray = array();
        foreach ($array as $item) {
            $jsonArray[] = $item->getInArray();
        }
        return $jsonArray;
    }

    /**
     * @param EntityManager $em
     * @return EntityManager
     * @throws \Doctrine\ORM\ORMException
     */
    public static function getEntityManager(EntityManager $em) {
        if (!$em->isOpen()) {
            $em = $em->create(
                $em->getConnection(), $em->getConfiguration());
        }
        return $em;
    }
}