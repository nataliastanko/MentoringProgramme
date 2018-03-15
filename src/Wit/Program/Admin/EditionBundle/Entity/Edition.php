<?php

namespace Wit\Program\Admin\EditionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Edition.
 *
 * @ORM\Table(
 *     name="edition",
 *     indexes={
 * @ORM\Index(name="edition_name",                                                   columns={"name"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Wit\Program\Admin\EditionBundle\Repository\EditionRepository")
 */
class Edition
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
     * @Assert\NotBlank(
     *     message = "name.notBlank",
     *     groups={"settings"}
     * )
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /*
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @ORM\OneToMany(targetEntity="Wit\Program\Admin\EditionBundle\Entity\Person", mappedBy="edition")
     **/
    private $persons;

    /**
     * @ORM\ManyToMany(targetEntity="Wit\Program\Admin\EditionBundle\Entity\Partner", mappedBy="editions")
     **/
    private $partners;

    /**
     * @ORM\ManyToMany(targetEntity="Wit\Program\Admin\EditionBundle\Entity\Mentor", mappedBy="editions")
     **/
    private $mentors;

    /**
     * @ORM\ManyToMany(targetEntity="Wit\Program\Admin\EditionBundle\Entity\Sponsor", mappedBy="editions")
     **/
    private $sponsors;

    public function __construct()
    {
        // $this->mentors = new ArrayCollection();
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
     * Set name.
     *
     * @param string $name
     *
     * @return Partner
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

    public function getPersons()
    {
        return $this->persons;
    }

    public function getPartners()
    {
        return $this->partners;
    }

    public function getSponsors()
    {
        return $this->sponsors;
    }

    public function getMentors() {
        return $this->mentors;
     }

    public function __call($method, $arguments)
    {
        return \Symfony\Component\PropertyAccess\PropertyAccess::createPropertyAccessor()
            ->getValue($this->translate(), $method);
    }
}
