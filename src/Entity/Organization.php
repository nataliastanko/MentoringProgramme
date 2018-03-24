<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\Common\Collections\Criteria;

/**
 * Organization
 *
 * @todo add contact section here (contact email, partners apply email)
 * @todo add uploadable org logo
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @ORM\Table(name="organizations")
 * @ORM\Entity(repositoryClass="Repository\OrganizationRepository")
 */
class Organization
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
     *
     * @ORM\Column(name="subdomain", type="string", length=15, unique=true)
     */
    private $subdomain;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * If is accepted to the program by superadmin
     * @ORM\Column(name="is_accepted", type="boolean", options={"default" = false})
     */
    private $isAccepted;

    /**
     * @ORM\OneToMany(targetEntity="SectionConfig", mappedBy="organization")
     **/
    private $sections;

    /*
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    public function __construct()
    {
        $this->isAccepted = false;
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
     * Set subdomain.
     *
     * @param string $subdomain
     *
     * @return Organization
     */
    public function setSubdomain($subdomain)
    {
        $this->subdomain = $subdomain;

        return $this;
    }

    /**
     * Get subdomain.
     *
     * @return string
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     * Set country.
     *
     * @param string $country
     *
     * @return Organization
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Organization
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

    public function setIsAccepted($var)
    {
        $this->isAccepted = $var;
    }

    public function getIsAccepted()
    {
        return $this->isAccepted;
    }

    /**
     * Get menu sections
     * @return ArrayCollection
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Get enabled menu sections
     * @return ArrayCollection
     */
    public function getSectionsEnabled()
    {
        $criteria = Criteria::create()->where(
            Criteria::expr()->eq('isEnabled', true)
        );

        $sections = $this->getSections()->matching($criteria);

        return $sections;
    }

    /**
     * Get enabled sections
     * @return array
     */
    public function getSectionsEnabledArray()
    {
        $enabledSections = [];

        foreach ($this->getSectionsEnabled() as $i) {
            $enabledSections[$i->getSection()] = true;
        }

        return $enabledSections;
    }

    /**
     * Get buttons that are enabled
     * from enabled sections
     * @return array
     */
    public function getButtonsSectionsEnabledArray()
    {
        $enabledButtons = [];

        foreach ($this->getSectionsEnabled() as $i) {
            if (
                $i->getSection() === 'mentees'
                ||
                $i->getSection() === 'partners'
                ||
                $i->getSection() === 'mentors'
            ) {
                $enabledButtons[$i->getSection()] = true;
            }
        }

        return $enabledButtons;
    }
}
