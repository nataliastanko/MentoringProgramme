<?php

namespace Wit\Program\Admin\EditionBundle\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use Gedmo\Translatable\TranslatableListener;

class MentorMultifunctionalRepository extends SortableRepository
{
    /**
     * Fetch mentors without edition
     * @return ArrayCollection
     */
    public function getWithoutEdition()
    {
        return $this->createQueryBuilder('m') // mentor
            ->select('m')
            ->where('m.editions is empty')

            // ->leftJoin('m.editions', 'e') // manytomany junction table

            // it works
            // ->leftJoin('m.translations', 't') // translatable
            // ->addSelect('t')
            // ->where(
            //     $qb->expr()->isNull('e.id')
            // )

            // it works
            // ->having('COUNT(e.id) = 0')
            // ->groupBy('m.id')

            ->orderBy('m.id', 'asc')
            ->getQuery()
            ->getResult();
    }

    /**
     * Fetch mentors from edition
     * @param  integer $id edition id
     * @return ArrayCollection
     */
    public function getFromEdition($id, $orderAlpha = false, $withInvitations = false)
    {
        $query = $this->getFromEditionQuery($id, $orderAlpha, $withInvitations)
            ->getQuery();

        if ($withInvitations) {
            $query->setHint(\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, true);
        }

        return $query->getResult();
    }

    /**
     * Fetch mentors ordered by sortable position
     * mentors that are accepted in edition
     * belongs to given edition
     * @param  integer $id edition id
     * @return QueryBuilder
     */
    public function getFromEditionQuery($id, $orderAlpha = false, $withInvitations = false)
    {
        $qb = $this->createQueryBuilder('m'); // mentor

        $qb->select('m')
            ->innerJoin('m.editions', 'e') // manytomany junction table
            ->leftJoin('m.translations', 't') // translatable
            ->addSelect('t');
        if ($withInvitations) {
            $qb->leftJoin('m.invitation', 'i') // we need also nulls
            // ->addSelect('i')
            ->addSelect("partial i.{id, isAccepted}") // reduces the number of queries
            ;
        }
            $qb->where(
                $qb->expr()->eq('e.id', '?1')
            )
            ->setParameter(1, $id)
            ->leftJoin('m.persons', 'p')
            ->addSelect('p')// reduces the number of queries
            ;

        if ($orderAlpha) {
            $qb
                ->addOrderBy('m.lastName', 'ASC')
                ->addOrderBy('m.name', 'ASC')
                ;
        } else { // default sort
            $qb->orderBy('m.position', 'asc');
        }

        return $qb;
    }

    public function listSubmissionsQuery()
    {
        return $this->createQueryBuilder('m') // mentor
            ->select('m')
            ->leftJoin('m.editions', 'e') // inner join wont get mentors without edition
            ->addSelect('e') // reduces number of queries
            // ->orderBy('m.createdAt', 'desc')
            ->orderBy('m.id', 'desc') // order by submission order
            ;
    }

    /**
     * Check if there are mentors without mentees
     * @param  integer $id edition id
     * @param  boolean $false filter by if mentor has chosen a mentee
     * @return ArrayCollection
     */
    public function findMentorsWithoutMentees($id, $chosen = false)
    {
        return $this->findMentorsWithoutMenteesQuery($id, $chosen)
            ->getQuery()->getResult();
    }

    /**
     * Check if there are mentors without mentees
     * @param  integer $id edition id
     * @param  boolean $false filter by if mentor has chosen a mentee
     * @return QueryBuilder
     */
    public function findMentorsWithoutMenteesQuery($id, $chosen = false)
    {
        $qb = $this->createQueryBuilder('m'); // mentor

        $qb->select('m')
            ->innerJoin('m.editions', 'e') // inner join wont get mentors without edition
            ->where(
                $qb->expr()->eq('e.id', '?1')
            )
            ->setParameter(1, $id)
            ->leftJoin('m.persons', 'p')

            ->addOrderBy('m.lastName', 'ASC')
            ->addOrderBy('m.name', 'ASC')
        ;

        // find only mentors that did not choose
        if ($chosen) {
            $em = $this->getEntityManager();
            // look for information if mentor chosen a mentee
            // construct a subselect joined with an entity
            // that have a relation with the first table
            $sub = $em->createQueryBuilder();
            $sub->select('sp')
                ->from('WitProgramAdminEditionBundle:Person', 'sp')
                ->leftJoin('sp.mentor', 'sm')
                // złączenie w subquery na mentorze,
                // porównujemy wiersze mentorów (to wiersze mentorów chcemy ostatecznie zwrócić)
                ->where('sm.id = m.id')
                ->innerJoin('sp.edition', 'spe')
                ->andWhere(
                    $qb->expr()->eq('spe.id', '?1')
                )
                ->setParameter(1, $id)
                ->andWhere(
                    $sub->expr()->eq('sp.isChosen', true)
                )
                // ->andWhere(
                //     $sub->expr()->eq('sp.isAccepted', true)
                // )
            ;

            // $qb->andWhere(
            //     $qb->expr()->eq('p.isAccepted', true)
            // );

            // pobierz tylko tych mentorów,
            // ktorzy nie mają podzbioru uczestniczek z isChosen
            // (dokładnie to podzbiór jednoelementowy)
            $qb->andWhere(
                $qb->expr()->not(
                    $qb->expr()->exists(
                        $sub->getDQL()
                    )
                )
            );

        } else {
            $em = $this->getEntityManager();
            // look for information if mentor chosen a mentee
            // construct a subselect joined with an entity
            // that have a relation with the first table
            $sub = $em->createQueryBuilder();
            $sub->select('sp')
                ->from('WitProgramAdminEditionBundle:Person', 'sp')
                ->leftJoin('sp.mentor', 'sm')
                // złączenie w subquery na mentorze,
                // porównujemy wiersze mentorów (to wiersze mentorów chcemy ostatecznie zwrócić)
                ->where('sm.id = m.id')
                ->innerJoin('sp.edition', 'spe')
                ->andWhere(
                    $qb->expr()->eq('spe.id', '?1')
                )
                ->setParameter(1, $id)
                ->andWhere(
                    $qb->expr()->eq('sp.isAccepted', true)
                )
            ;

            // pobierz tylko tych mentorów,
            // ktorzy nie mają podzbioru uczestniczek z chocby jednym isAccepted
            // (dokładnie to podzbiór jednoelementowy)
            $qb->andWhere(
                $qb->expr()->not(
                    $qb->expr()->exists(
                        $sub->getDQL()
                    )
                )
            );
        }

        return $qb;
    }
}
