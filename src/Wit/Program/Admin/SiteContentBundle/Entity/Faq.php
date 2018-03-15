<?php

namespace Wit\Program\Admin\SiteContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Knp\DoctrineBehaviors\Model\Translatable\Translatable;

/**
 * Faq.
 *
 * @ORM\Table(name="faq")
 * @ORM\Entity(repositoryClass="Wit\Program\Admin\EditionBundle\Repository\MultifunctionalRepository")
 */
class Faq
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

    /**
     * Set position.
     *
     * @param int $position
     *
     * @return Faq
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    // public function __call($method, $arguments)
    // {
    //     return $this->proxyCurrentLocaleTranslation($method, $arguments);
    // }

    // or do it with PropertyAccessor that ships with Symfony SE
    // if your methods don't take any required arguments
    public function __call($method, $arguments)
    {
        return \Symfony\Component\PropertyAccess\PropertyAccess::createPropertyAccessor()
            ->getValue($this->translate(), $method);
    }

    // public function getContent() {
    //     if ($content = $this->translate()->getContent()) {
    //         return $content;
    //     }

    //     return '';
    // }
}
