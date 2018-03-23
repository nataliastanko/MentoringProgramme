<?php

namespace Repository;

/**
 * ConfigRepository
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class ConfigRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find config row
     * @return Config|null
     */
    public function findConfig()
    {
        $gb = $this->createQueryBuilder('c')
            ->select('c')
            ->setMaxResults(1);
        return $gb->getQuery()->getOneOrNullResult();
    }
}
