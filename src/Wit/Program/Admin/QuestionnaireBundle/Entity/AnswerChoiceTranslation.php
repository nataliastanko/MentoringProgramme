<?php

namespace Wit\Program\Admin\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Translatable\Translation;

/**
 * AnswerChoiceTranslation.
 *
 * @todo rename table name to answer_choices_translations
 * @ORM\Table(
 *     name="choices_translations",
 *     indexes={
 * @ORM\Index(name="name_idx", columns={"name"})}
 * )
 * @ORM\Entity()
 */
class AnswerChoiceTranslation
{
    use Translation;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Choice
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
