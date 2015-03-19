<?php

namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
}