<?php

namespace Wit\Program\Admin\EditionBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;

/**
 * !!! You are already inside an EntityRepository,
 * you don't need to call $this->getEntityManager()
 *
 * https://asterionalfagmailcom-demo.readthedocs.io/en/stable/reference/query-builder.html#the-expr-class
 *
 * edition has name and person has name
 */
class PersonRepository extends EntityRepository
{
     /**
     * Fetch persons from edition
     * @param  integer $id edition id
     * @param  boolean $isChosen if mentee is chosen
     * @return ArrayCollection
     */
    public function getFromEdition($id, $isChosen = false)
    {
        return $this->getFromEditionQuery($id, $isChosen)
            ->getQuery()->getResult();
    }

    /**
     * Fetch persons
     * belongs to given edition
     * @param  integer $id edition id
     * @param  boolean $isChosen if mentee is chosen
     * @return QueryBuilder
     */
    public function getFromEditionQuery($id, $isChosen = false)
    {
         $qb = $this->createQueryBuilder('p'); // person

         $qb->select('p')
            ->innerJoin('p.edition', 'e') // manytomany junction table
            ->addSelect('e')
            ->where(
                $qb->expr()->eq('e.id', '?1')
            )
            ->setParameter(1, $id)
            ->orderBy('p.id', 'desc')
            ;

        if ($isChosen) {
            $qb->andWhere(
                $qb->expr()->eq('p.isChosen', true)
            )
            ->innerJoin('p.mentor', 'm')
            ->addSelect('m')
            ;
        }

        return $qb;
    }

    public function filterByMentorAndEdition($mentorId, $editionId, $isAccepted = false)
    {
        // http://docs.doctrine-project.org/en/latest/reference/working-with-associations.html#filtering-collections

        $expr = Criteria::expr();
        $criteria = Criteria::create();
        $criteria
            ->where(
                $expr->andX(
                    $expr->eq('mentor', $mentorId),
                    $expr->eq('edition', $editionId)
                )
            )
            ->orderBy(['createdAt' => Criteria::DESC])
            ;

        if ($isAccepted) {
            $criteria
                ->andWhere(
                    $expr->eq('isAccepted', $isAccepted)
                )
            ;
        }

        return $this->matching($criteria);
    }

}
