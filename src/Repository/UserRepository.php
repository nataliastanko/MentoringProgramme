<?php

namespace Repository;

// use Doctrine\Common\Collections\Criteria;

/**
 * UserRepository.
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Fetch users:
     * from given edition
     * with given invitation id
     * @param  int $editionId edition id
     * @param  int $invitationId invitation id
     * @param  string $participantType invitation id
     * @return ArrayCollection
     */
    public function getEventParticipants($editionId, $invitationId, $participantType)
    {
        return $this->getEventParticipantsQuery($editionId, $invitationId, $participantType);
    }

    /**
     * Fetch users:
     * from given edition
     * with given invitation id
     * @param  int $editionId edition id
     * @param  int $invitationId invitation id
     * @param  string $participantType invitation id
     * @return QueryBuilder
     */
    public function getEventParticipantsQuery($editionId, $invitationId, $participantType)
    {
        $qb = $this->createQueryBuilder('u'); // mentor

        $qb->select('u')
            ->leftJoin('u.invitation', 'i')
            ->where(
                $qb->expr()->eq('i.id', '?1')
            )
            ->setParameter(1, $invitationId);

        if ($participantType === 'mentor') {
            $qb
            ->leftJoin('i.mentor', 'm')
            ->innerJoin('m.editions', 'e')
            ;
        } else if ($participantType === 'mentee') {
            $qb
            ->leftJoin('i.person', 'p') // get mentee
            ->innerJoin('p.edition', 'e')
            ;
        }

        $qb->andWhere(
            $qb->expr()->eq('e.id', '?2')
        )->setParameter(2, $editionId);

        return $qb;
    }
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
    // static public function createEventsCriteria(\DateTime $startDate, \DateTime $endDate)
    // {
    //     $criteria = Criteria::create();
    //     $expr = Criteria::expr();

    //     $compareTimeFrom = $startDate->format('Y-m-d H:i:s');
    //     $compareTimeTo = $endDate->format('Y-m-d H:i:s');

    //     return $criteria
    //         ->where(
    //             $expr->orX(
    //                 $expr->andX( // in bounds the range
    //                     $expr->gte('startTime', $compareTimeFrom),
    //                     $expr->lte('endTime', $compareTimeFrom)
    //                 ),
    //                 $expr->andX( // beyond the range both sides
    //                     $expr->lt('startTime', $compareTimeFrom),
    //                     $expr->gt('endTime', $compareTimeTo)
    //                 ),
    //                 $expr->andX( // beyond the range future
    //                     $expr->gte('startTime', $compareTimeFrom),
    //                     $expr->lte('startTime', ':compareTimeTo'),
    //                     $expr->gte('endTime', $compareTimeTo)
    //                 ),
    //                 $expr->andX( // beyond the range past
    //                     $expr->lte('startTime', $compareTimeFrom),
    //                     $expr->gte('endTime', $compareTimeFrom),
    //                     $expr->lte('endTime', $compareTimeTo)
    //                 )
    //             )
    //         )
    //         ->orderBy(['startTime' => Criteria::ASC]);
    //     ;
    // }

}
