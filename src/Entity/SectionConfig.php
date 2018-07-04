<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Annotation\Doctrine\OrganizationAware;

/**
 * Sections config
 * Defines which sections are enabled (active) for organization
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="section_config")
 * @OrganizationAware(organizationFieldName="organization_id")
 */
class SectionConfig
{
    /**
     * About:
     * organization
     * program
     * signup
     * dates
     *
     * @todo about per edition
     */
    const sectionAbout = 'about';

    /**
     * Organization's program's rules paragraphs
     *
     * @todo rules per edition
     */
    const sectionRules = 'rules';

    /**
     * Organization's program's FAQ paragraphs
     */
    const sectionFaq = 'faq';

    /**
     * Enables:
     * Mentees signup (internal system)
     * Mentees management
     * Mentee - mentor management
     * Mentees accounts
     */
    const sectionMentees = 'mentees';

    /**
     * Enables:
     * Ability to signup via external form instead of using internal system
     * External db, external system option
     */
    const menteesExternalSignup = 'menteesExternalSignup';

    /**
     * Enables:
     * Partners signup ability
     * Adding partners manually
     * Dispaying partners on organization page
     */
    const sectionPartners = 'partners';

    /**
     * Enables:
     * Adding sponsors manually
     * Dispaying sponsors on organization page
     */
    const sectionSponsors = 'sponsors';

    /**
     * Enables:
     * Mentors signup
     * Adding mentors manually
     * Dispaying mentors on organization page
     * Mentors accounts
     */
    const sectionMentors = 'mentors';

    /**
     * Enables:
     * Mentors faq
     *
     * Runs only if sectionMentors enabled
     */
    const sectionMentorfaq = 'mentorfaq';
    const sectionGallery = 'gallery';

    /**
     * Enables:
     * Mentee - mentor calendar
     *
     * Runs only if sectionMentors or sectionMentees enabled
     */
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
