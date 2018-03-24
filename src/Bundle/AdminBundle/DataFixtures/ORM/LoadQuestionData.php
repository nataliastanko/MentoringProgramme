<?php

namespace AdminBundle\FataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Entity\Question;
use Entity\AnswerChoice;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class LoadQuestionData implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
}
