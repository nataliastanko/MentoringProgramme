<?php

namespace Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;
use Doctrine\Common\Collections\ArrayCollection;
// use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Entity\Organization;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class MultifunctionalRepository extends SortableRepository
{
    /**
     * Fetch objects from edition
     * @param  integer $id edition id
     * @return ArrayCollection
     */
    public function getFromEdition($id)
    {
        return $this->getFromEditionQuery($id)->getQuery()->getResult();
    }

    /**
     * Fetch objects ordered by sortable position
     * belongs to given edition
     * with translations if any
     * @TODO check if object has reltion with edition
     * @param  integer $id edition id
     * @return QueryBuilder
     */
    public function getFromEditionQuery($id)
    {
        $qb = $this->createQueryBuilder('o')
            ->select('o')
            ->leftJoin('o.editions', 'e')
            ;

        // if translatable then select also translations
        if (method_exists($this->getClassName(), "getTranslationEntityClass")) {
            $qb
            ->leftJoin('o.translations', 't') // translatable
            ->addSelect('t')
            ;
        }

        // @TODO if has edition
        $qb->where(
            $qb->expr()->eq('e.id', '?1')
        )
        ->setParameter(1, $id)
        ;

        // @TODO if has sortable field
        // make sure to make use from sortable
        return $qb->orderBy('o.position', 'asc');
    }

    public function getAll()
    {
        $qb = $this->createQueryBuilder('o')
            ->select('o')
            ;

        // if translatable then select also translations
        if (method_exists($this->getClassName(), "getTranslationEntityClass")) {
            $qb
                ->leftJoin('o.translations', 't') // translatable
                ->addSelect('t')
                ;
        }

        // @TODO if has sortable field
        // make sure to make use from sortable
        $qb->orderBy('o.position', 'asc');

        return $qb->getQuery()->getResult();
    }

    // not currently used
    // replaced with @OrganizationAware
    // public function filterByOrganization(Organization $org)
    // {
    //     $criteria = Criteria::create();
    //     $criteria->where(Criteria::expr()->eq('organization', $org));

    //     return $this->matching($criteria);
    // }
}
