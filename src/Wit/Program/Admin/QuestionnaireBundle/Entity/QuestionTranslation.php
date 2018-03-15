<?php

namespace Wit\Program\Admin\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Translatable\Translation;

/**
 * QuestionTranslation.
 *
 * @ORM\Table(name="questions_translations")
 * @ORM\Entity()
 */
class QuestionTranslation
{
    use Translation;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * Field description
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $helpblock;

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Question
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

    /**
     * Set helpblock.
     *
     * @param string $helpblock
     *
     * @return Question
     */
    public function setHelpblock($helpblock)
    {
        $this->helpblock = $helpblock;

        return $this;
    }

    /**
     * Get helpblock.
     *
     * @return string
     */
    public function getHelpblock()
    {
        return $this->helpblock;
    }
}
