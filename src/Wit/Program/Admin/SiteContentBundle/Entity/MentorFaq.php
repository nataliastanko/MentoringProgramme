<?php

namespace Wit\Program\Admin\SiteContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Knp\DoctrineBehaviors\Model\Translatable\Translatable;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * MentorFaq.
 *
 * @ORM\Table(name="mentor_faq")
 * @ORM\Entity(repositoryClass="Wit\Program\Admin\EditionBundle\Repository\MultifunctionalRepository")
 */
class MentorFaq
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
     * @var int
     * @Gedmo\SortablePosition
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function __call($method, $arguments)
    {
        return PropertyAccess::createPropertyAccessor()
            ->getValue($this->translate(), $method);
    }
}
