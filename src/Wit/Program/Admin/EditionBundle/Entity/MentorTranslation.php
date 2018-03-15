<?php

namespace Wit\Program\Admin\EditionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Translatable\Translation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MentorTranslation.
 *
 * @ORM\Table(name="mentors_translations")
 * @ORM\Entity()
 */
class MentorTranslation
{
    use Translation;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $bio;

    /**
     * @var string
     *
     * Warning: length=65535 only on creation
     * Doctrine does not update existing text fields types (TEXT, MEDIUMTEXT, LONGTEXT)
     *
     * @TODO translation with parameter
     *
     * @Assert\Length(
     *      min = 2,
     *      max = 120,
     *      minMessage = "mentor.occupation.min_length",
     *      maxMessage = "mentor.occupation.max_length",
     *      groups={"settings"}
     * )
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private $occupation;

    /**
     * Set bio.
     *
     * @param string $bio
     *
     * @return Mentor
     */
    public function setBio($bio)
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Get bio.
     *
     * @return string
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Set occupation.
     *
     * @param string $occupation
     *
     * @return Mentor
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;

        return $this;
    }

    /**
     * Get occupation.
     *
     * @return string
     */
    public function getOccupation()
    {
        return $this->occupation;
    }
}
