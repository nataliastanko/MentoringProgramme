<?php

namespace Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class TranslatableRepository extends EntityRepository
{
    /**
     * Get answer choices with translations
     * @return ArrayCollection
     */
    public function getAnswerChoices()
    {
        $qb = $this->createQueryBuilder('ac')
            ->select('ac')

        // if translatable then select also translations
        // if (method_exists($this->getClassName(), "getTranslationEntityClass")) {
            ->leftJoin('ac.translations', 't') // translatable
            ->addSelect('t')
            ;
        // }

        return $qb->getQuery()->getResult();
    }
}
