<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Annotation\Doctrine\OrganizationAware;

/**
 * Sections config
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="section_config")
 * @OrganizationAware(organizationFieldName="organization_id")
 */
class SectionConfig
{
    const sectionAbout = 'about';
    const sectionRules = 'rules';
    const sectionFaq = 'faq';
    const sectionMentees = 'mentees';
    const sectionPartners = 'partners';
    const sectionSponsors = 'sponsors';
    const sectionMentors = 'mentors';
    const sectionMentorfaq = 'mentorfaq';
    const sectionGallery = 'gallery';
    const sectionCalendar = 'calendar';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Section name
     *
     * @var string
     *
     * @ORM\Column(name="section", type="string", length=50)
     */
    private $section;

    /**
     * If section is enabled for given organization
     *
     * @var bool
     *
     * @ORM\Column(name="is_enabled", type="boolean", options={"default" = false})
     */
    private $isEnabled;

    /**
     * Belongs to organization
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="sections")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=false)
     */
    private $organization;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set section
     *
     * @param boolean $section
     *
     * @return Config
     */
    public function setSection($var)
    {
        $this->section = $var;

        return $this;
    }

    /**
     * Get section
     *
     * @return bool
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set isEnabled
     *
     * @param boolean $isEnabled
     *
     * @return Config
     */
    public function setIsEnabled($var)
    {
        $this->isEnabled = $var;

        return $this;
    }

    /**
     * Get isEnabled
     *
     * @return bool
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    public function setOrganization($var)
    {
        $this->organization = $var;

        return $this;
    }
}
