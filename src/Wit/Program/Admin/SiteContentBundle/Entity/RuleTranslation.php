<?php

namespace Wit\Program\Admin\SiteContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Translatable\Translation;

/**
 * RuleTranslation.
 *
 * @ORM\Table(name="rules_translations")
 * @ORM\Entity()
 */
class RuleTranslation
{
    use Translation;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return Rule
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
}
