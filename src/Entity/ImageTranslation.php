<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Translatable\Translation;

/**
 * ImageTranslation.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @ORM\Table(name="images_translations")
 * @ORM\Entity()
 */
class ImageTranslation
{
    use Translation;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Mentor
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
