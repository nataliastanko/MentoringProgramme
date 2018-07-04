<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Annotation\Doctrine\OrganizationAware;

/**
 * Config
 * Switching configuration only.
 *
 * @todo signup belongs to edition
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @ORM\Table(name="config")
 * @ORM\Entity(repositoryClass="Repository\ConfigRepository")
 * @OrganizationAware(organizationFieldName="organization_id")
 *
 * @Assert\Callback({"AdminBundle\Form\Constraint\Callback\ConfigCallback", "checkPartnersEmail"})
 * @Assert\Callback({"AdminBundle\Form\Constraint\Callback\ConfigCallback", "checkMenteesExternalSignupUrl"})
 */
class Config
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
     * Internal mentors signup (via internal form)
     * @var bool
     *
     * @ORM\Column(name="is_signup_mentors_enabled", type="boolean", options={"default" = false})
     */
    private $isSignupMentorsEnabled;

    /**
     * Partners signup (via mailto link)
     *
     * @todo rename to is_signup_partners_enabled when moving to organization
     * @var bool
     *
     * @ORM\Column(name="is_signup_partners_enabled", type="boolean", options={"default" = false})
     */
    private $isSignupPartnersEnabled;

    /**
     * Mentees signup, both via:
     *  - internal form (SectionConfig sectionMentees enabled)
     *  - external form (SectionConfig menteesExternalSignup enabled)
     *
     * @var bool
     *
     * @ORM\Column(name="is_signup_mentees_enabled", type="boolean", options={"default" = false})
     */
    private $isSignupMenteesEnabled;

    /**
     * Belongs to organization
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="Organization")
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
     * Set isSignupMentorsEnabled
     *
     * @param boolean $isSignupMentorsEnabled
     *
     * @return Config
     */
    public function setIsSignupMentorsEnabled($var)
    {
        $this->isSignupMentorsEnabled = $var;

        return $this;
    }

    /**
     * Get isSignupMentorsEnabled
     *
     * @return bool
     */
    public function getIsSignupMentorsEnabled()
    {
        return $this->isSignupMentorsEnabled;
    }

    /**
     * Set isSignupPartnersEnabled
     *
     * @param boolean $isSignupPartnersEnabled
     *
     * @return Config
     */
    public function setIsSignupPartnersEnabled($var)
    {
        $this->isSignupPartnersEnabled = $var;

        return $this;
    }

    /**
     * Get isSignupPartnersEnabled
     *
     * @return bool
     */
    public function getIsSignupPartnersEnabled()
    {
        return $this->isSignupPartnersEnabled;
    }

    /**
     * Set isSignupMenteesEnabled
     *
     * @param boolean $isSignupMenteesEnabled
     *
     * @return Config
     */
    public function setIsSignupMenteesEnabled($var)
    {
        $this->isSignupMenteesEnabled = $var;

        return $this;
    }

    /**
     * Get isSignupMenteesEnabled
     *
     * @return bool
     */
    public function getIsSignupMenteesEnabled()
    {
        return $this->isSignupMenteesEnabled;
    }

    public function setOrganization($var)
    {
        $this->organization = $var;

        return $this;
    }

    /**
     * Get organization
     *
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Final buttons
     * Enabled buttons from enabled sections
     * @return array
     */
    public function getEnabledButtons()
    {
        /**
         * Get organization's main page buttons that are enabled
         * based on enabled sections
         * @var array
         */
        $buttonsSectionsEnabled = $this->getOrganization()->getButtonsSectionsEnabledArray();

        $enabled = [];

        /**
         * Handle mentees signup button, there are 2 possibilities for mentees signup
         */
        if ($this->isSignupMenteesEnabled) {
            // signup via external form
            if (isset($buttonsSectionsEnabled['menteesExternalSignup'])) {
                $enabled['menteesExternalSignup'] = true;
            // signup via external form
            } else if (isset($buttonsSectionsEnabled['mentees'])) {
                $enabled['mentees'] = true;
            }
        }

        if ($this->isSignupMentorsEnabled && isset($buttonsSectionsEnabled['mentors'])) {
            $enabled['mentors'] = true;
        }

        if ($this->isSignupPartnersEnabled && isset($buttonsSectionsEnabled['partners'])) {
            $enabled['partners'] = true;
        }

        return $enabled;
    }

}

