<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Translatable\Translation;

/**
 * FaqTranslation.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @ORM\Table(name="faq_translations")
 * @ORM\Entity()
 */
class FaqTranslation
{
    use Translation;

    /**
     * @var string
     *
     * @ORM\Column(name="question", type="text", length=65535)
     */
    private $question;

    /**
     * @var string
     *
     * @ORM\Column(name="answer", type="text", length=65535)
     */
    private $answer;

    /**
     * Set answer.
     *
     * @param string $answer
     *
     * @return Faq
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer.
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set question.
     *
     * @param string $question
     *
     * @return Faq
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
}
