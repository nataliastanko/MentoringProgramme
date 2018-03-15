<?php

namespace Wit\Program\Admin\QuestionnaireBundle\FataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Wit\Program\Admin\QuestionnaireBundle\Entity\Question;
use Wit\Program\Admin\QuestionnaireBundle\Entity\AnswerChoice;

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
