<?php

namespace Wit\Program\Admin\EditionBundle\Repository;

/**
 * ConfigRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
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
