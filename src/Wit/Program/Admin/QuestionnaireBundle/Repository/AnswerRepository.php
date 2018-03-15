<?php

namespace Wit\Program\Admin\QuestionnaireBundle\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;
use Doctrine\ORM\QueryBuilder;

class AnswerRepository extends SortableRepository
{
    /**
     * Get persons answers
     * @param  int $id person id
     * @return ArrayCollection
     */
    public function filterByPerson($id)
    {
        $qb = $this->createQueryBuilder('a');

        $qb = $this->filterByPersonQuery($qb, $id);

        $qb = $this->joinTranslationsToQuestionsQuery($qb);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get mentors answers
     * @param  int $id mentor id
     * @return ArrayCollection
     */
    public function filterByMentor($id)
    {
        $qb = $this->createQueryBuilder('a');

        $qb = $this->filterByMentorQuery($qb, $id);

        $qb = $this->joinTranslationsToQuestionsQuery($qb);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return QueryBuilder
     * @param  int $id mentor id
     * @return QueryBuilder
     */
    public function filterByMentorQuery(QueryBuilder $qb, $id)
    {
        return $qb
            ->leftJoin('a.mentor', 'm')
            ->addSelect('m')
            ->where(
                $qb->expr()->eq('m.id', '?1')
            )
            ->setParameter(1, $id)
        ;
    }

    /**
     * @param QueryBuilder $qb
     * @param int $id mentor id
     * @return QueryBuilder
     */
    public function filterByPersonQuery(QueryBuilder $qb, $id)
    {
        return $qb
            ->leftJoin('a.person', 'p')
            ->addSelect('p')
            ->where(
                $qb->expr()->eq('p.id', '?1')
            )
            ->setParameter(1, $id)
        ;
    }

    /**
     * Join translations and answer choices
     * so lazy loading wont not kill yur website
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function joinTranslationsToQuestionsQuery(QueryBuilder $qb)
    {
        return $qb
            ->select('a')
            ->leftJoin('a.question', 'q')
            ->addSelect('q')

            ->leftJoin('a.answerChoice', 'ac')
            ->addSelect('ac')

            ->leftJoin('q.translations', 'qt') // translatable
            ->addSelect('qt')

            ->leftJoin('ac.translations', 'act')
            ->addSelect('act')

            ->orderBy('q.position', 'asc')
            ;
    }

}
