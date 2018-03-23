<?php

namespace Repository;

use Doctrine\Common\Collections\Criteria;
// use Doctrine\ORM\Query\Expr;
use Doctrine\Common\Collections\ArrayCollection;
use Entity\User;
/**
 * EventRepository.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class EventRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param  User      $user
     * @param  \DateTime $startDate
     * @param  \DateTime $endDate
     * @return Event[]
     */
    public function filterEvents(User $user, \DateTime $startDate = null, \DateTime $endDate = null)
    {
        $em = $this->getEntityManager();

        // automatically selects everything
        $qb = $em->createQueryBuilder();

        // select
        $qb->select('e')
        // ->select('e.id', 'e.author')
        // ->addSelect('u.id', 'u.name', 'u.lastName') // selects but store it internally
            ->from('Entity:Event', 'e')
            ->leftJoin('e.participant', 'p')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->in('p.id', ':user_id'),
                    $qb->expr()->in('e.author', ':user_id')
                )
            )
            ->setParameter('user_id', $user->getId())
            ;
        //     ->leftJoin('e.participants', 'p')
        // // filter by participant or author
        //     ->where(
        //         $qb->expr()->orX(
        //             $qb->expr()->in('p.id', ':users_ids'),
        //             $qb->expr()->in('e.author', ':users_ids')
        //         )
        //     )
        //     ->setParameter('users_ids', [$user->getId()]);

        if ($startDate && $endDate) {
            $qb->andWhere(
                $qb->expr()->orX(
                    // 'e.startTime BETWEEN :compareTimeFrom and :compareTimeTo',
                    // 'e.endTime BETWEEN :compareTimeFrom and :compareTimeTo'
                    $qb->expr()->between('e.startTime', ':compareTimeFrom', ':compareTimeTo'),
                    $qb->expr()->between('e.endTime', ':compareTimeFrom', ':compareTimeTo')
                )
            )
                ->setParameter('compareTimeFrom', $startDate->format('Y-m-d H:i:s'))
                ->setParameter('compareTimeTo', $endDate->format('Y-m-d H:i:s'));
        }

        $events = $qb
            ->orderBy('e.startTime', 'desc')
            ->getQuery()->getResult();

        return new ArrayCollection($events);
    }

    /**
     * Filter by day.
     *
     * @param  \DateTime $date
     * @return $this
     */
    public function filterByDay(User $user, \DateTime $date)
    {
        $startDate = clone $date;
        $startDate->setTime(0, 0, 0);

        $endDate = clone $date;
        $endDate->setTime(23, 59, 59);

        return $this->filterEvents($user, $startDate, $endDate);
    }

    /**
     * Filter by month.
     *
     * @param  \DateTime $date
     * @return $this
     */
    public function filterByMonth(User $user, \DateTime $date)
    {
        $startDate = clone $date;
        $startDate
            ->modify('first day of this month')
            ->setTime(0, 0, 0);

        $endDate = clone $date;
        $endDate
            ->modify('last day of this month')
            ->setTime(23, 59, 59);

        return $this->filterEvents($user, $startDate, $endDate);
    }

}
