<?php

namespace Wit\Program\Admin\EditionBundle\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;

class EditionRepository extends SortableRepository
{
    public function findLastEdition()
    {
        $gb = $this->createQueryBuilder('e')
            ->select('e')
            ->orderBy('e.position', 'desc')
            ->setMaxResults(1);
        return $gb->getQuery()->getOneOrNullResult();
    }

    /**
     * Embedded form query
     * @return Doctrine\ORM\QueryBuilder
     */
    public function getEditionsQueryBuilder()
    {
        return $this->createQueryBuilder('e')
            ->select('e')
            ->orderBy('e.position', 'desc');
    }

}
