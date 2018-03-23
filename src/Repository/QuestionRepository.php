<?php

namespace Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class QuestionRepository extends SortableRepository
{
    public function getFromType($type)
    {
        return $this->getFromTypeQuery($type)->getQuery()->getResult();
    }

    /**
     * @param  string $type type of question (to whom the question is directed)
     * @return QueryBuilder
     */
    public function getFromTypeQuery($type)
    {
        $qb = $this->createQueryBuilder('q');

        return $qb->select('q')

            // if translatable then select also translations
            // if (method_exists($this->getClassName(), "getTranslationEntityClass")) {
            ->leftJoin('q.translations', 't') // translatable
            ->addSelect('t')

            // ->leftJoin('q.answers', 'a') // translatable
            // ->leftJoin('a.translations', 'at') // translatable
            // ->addSelect('at')

            // ->innerJoin('q.answerChoices', 'ac') // reduces number of queries
            ->leftJoin('q.answerChoices', 'ac') // reduces number of queries

            ->addSelect('ac') // reduces number of queries
            ->leftJoin('ac.translations', 'act') // reduces number of queries
            ->addSelect('act') // reduces number of queries
            // }

            ->where(
                $qb->expr()->eq('q.type', '?1')
            )
            ->setParameter(1, $type)
            ->orderBy('q.position', 'asc')
            ;
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
}
