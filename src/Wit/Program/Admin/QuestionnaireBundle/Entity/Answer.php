<?php

namespace Wit\Program\Admin\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wit\Program\Admin\EditionBundle\Entity\Mentor;
use Wit\Program\Admin\EditionBundle\Entity\Person;

/**
 * Answer.
 * Answer contains personalized Person's/Mentor's answer
 * OR linked choice that fixed to Question
 *
 * @ORM\Table(name="answers")
 * @ORM\Entity(repositoryClass="Wit\Program\Admin\QuestionnaireBundle\Repository\AnswerRepository") *
 *
 */
class Answer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Question",
     *     inversedBy="answers"
     * )
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=false)
     **/
    private $question;

    /**
     * @TODO add validation when mentee applies
     *
     * @ORM\ManyToOne(
     *     targetEntity="Wit\Program\Admin\EditionBundle\Entity\Person",
     *     inversedBy="answers"
     * )
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     **/
    private $person;

    /**
     * @TODO add validation when mentor applies
     *
     * @ORM\ManyToOne(
     *     targetEntity="Wit\Program\Admin\EditionBundle\Entity\Mentor",
     *     inversedBy="answers"
     * )
     * @ORM\JoinColumn(name="mentor_id", referencedColumnName="id", nullable=true)
     **/
    private $mentor;

    /**
     * @TODO rename field to answer_choice_id
     * @ORM\ManyToOne(
     *     targetEntity="AnswerChoice",
     *     inversedBy="answers"
     * )
     * @ORM\JoinColumn(name="choice_id", referencedColumnName="id")
     **/
    private $answerChoice;

    /**
     * Set person.
     *
     * @param Person $person
     *
     * @return Answer
     */
    public function setPerson(Person $var)
    {
        $this->person = $var;

        return $this;
    }

    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @TODO this can change to relation with manytomany junction table editions_mentors
     * Set Mentor.
     *
     * @param Mentor $Mentor
     *
     * @return Answer
     */
    public function setMentor(Mentor $var)
    {
        $this->mentor = $var;

        return $this;
    }

    /**
     * @TODO this can change to relation with manytomany junction table editions_mentors
     */
    public function getMentor()
    {
        return $this->mentor;
    }

    /**
     * Set question.
     *
     * @param Question $question
     *
     * @return Answer
     */
    public function setQuestion(Question $var)
    {
        $this->question = $var;

        return $this;
    }

    public function getQuestion()
    {
        return $this->question;
    }

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
     * Set content.
     *
     * @param string $content
     *
     * @return Answer
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set AnswerChoice.
     *
     * @param AnswerChoice $var
     *
     * @return Answer
     */
    public function setAnswerChoice($var)
    {
        $this->answerChoice = $var;

        return $this;
    }

    /**
     * Get AnswerChoice.
     *
     * @return string
     */
    public function getAnswerChoice()
    {
        return $this->answerChoice;
    }
}
