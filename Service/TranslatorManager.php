<?php

namespace Calitarus\TranslatorBundle\Service;

use Doctrine\ORM\EntityManager;

use Calitarus\TranslatorBundle\Entity;


class TranslatorManager {

    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }


    public function availableTranslations() {
        $query = $this->em->createQuery('SELECT l.code AS code, l.name AS name FROM Calitarus\TranslatorBundle\Entity\Language l JOIN l.translations t');
        $lang = array();
        foreach ($query->getArrayResult() as $row) {
            $lang[$row['code']] = $row['name'];
        }
        return $lang;
    }

}
