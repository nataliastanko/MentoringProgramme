<?php

namespace Wit\Program\Admin\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Translatable\Translatable;

/**
 * AnswerChoice.
 * @TODO rename table to answer-choices
 *
 * @ORM\Table(name="choices")
 * @ORM\Entity(repositoryClass="Wit\Program\Admin\QuestionnaireBundle\Repository\TranslatableRepository") *
 */
class AnswerChoice
{
    use Translatable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="answerChoices", cascade={"persist"})
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     **/
    private $question;

    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="answerChoice", cascade={"all"})
     **/
    private $answers;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set question.
     *
     * @param string $question
     *
     * @return AnswerChoice
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question.
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    public function getName()
    {
        return $this->translate()->getName();
    }

    // /**
    //  * When owning side is on AnswerChoice
    //  * @param Question $question
    //  */
    // public function addQuestion(Question $question)
    // {
    //     $this->setQuestion($question);
    // }

    public function __call($method, $arguments)
    {
        return \Symfony\Component\PropertyAccess\PropertyAccess::createPropertyAccessor()
            ->getValue($this->translate(), $method);
    }
}
