<?php

namespace Wit\Program\Admin\UserBundle\Repository;

// use Doctrine\Common\Collections\Criteria;

/**
 * UserRepository.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{

    // Criteria Manytomany Doctrine 2.5.x bug,
    // Criteria do not handle manytomany at all for queries than use gte or lte
    // fix is coming in roadmap 2.6 fix with php 7.1
    // Manytomany inversed side with eg only: use ArrayCollection::matching() instead of PersistentCollection::matching() (which works fine because it's a simple array wrapper)
    // https://github.com/doctrine/doctrine2/issues/4910

    /**
     * To keep code organized
     * all of query logic is inside of repository classes
     * including Criteria
     *
     * @param  \DateTime $startDate [description]
     * @param  \DateTime $endDate   [description]
     * @return Criteria
     */
    static public function createEventsCriteria(\DateTime $startDate, \DateTime $endDate)
    {
        $criteria = Criteria::create();
        $expr = Criteria::expr();

        $compareTimeFrom = $startDate->format('Y-m-d H:i:s');
        $compareTimeTo = $endDate->format('Y-m-d H:i:s');

        return $criteria
            ->where(
                $expr->orX(
                    $expr->andX( // in bounds the range
                        $expr->gte('startTime', $compareTimeFrom),
                        $expr->lte('endTime', $compareTimeFrom)
                    ),
                    $expr->andX( // beyond the range both sides
                        $expr->lt('startTime', $compareTimeFrom),
                        $expr->gt('endTime', $compareTimeTo)
                    ),
                    $expr->andX( // beyond the range future
                        $expr->gte('startTime', $compareTimeFrom),
                        $expr->lte('startTime', ':compareTimeTo'),
                        $expr->gte('endTime', $compareTimeTo)
                    ),
                    $expr->andX( // beyond the range past
                        $expr->lte('startTime', $compareTimeFrom),
                        $expr->gte('endTime', $compareTimeFrom),
                        $expr->lte('endTime', $compareTimeTo)
                    )
                )
            )
            ->orderBy(['startTime' => Criteria::ASC]);
        ;
    }


}
