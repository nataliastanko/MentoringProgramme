<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Repository\Annotation\OrganizationAware;

/**
 * Config
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @ORM\Table(name="config")
 * @ORM\Entity(repositoryClass="Repository\ConfigRepository")
 * @OrganizationAware(organizationFieldName="organization_id")
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
     * @var bool
     *
     * @ORM\Column(name="is_signup_mentors_enabled", type="boolean", options={"default" = false})
     */
    private $isSignupMentorsEnabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_signup_pertaners_enabled", type="boolean", options={"default" = false})
     */
    private $isSignupPartnersEnabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_signup_mentees_enabled", type="boolean", options={"default" = false})
     */
    private $isSignupMenteesEnabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_chosen_mentees_visible", type="boolean", options={"default" = false})
     */
    private $isChosenMenteesVisible;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_users_accounts_enabled", type="boolean", options={"default" = false})
     */
    private $isUsersAccountsEnabled;

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

    /**
     * Set isChosenMenteesVisible
     *
     * @param boolean $isSignupMenteesEnabled
     *
     * @return Config
     */
    public function setIsChosenMenteesVisible($var)
    {
        $this->isChosenMenteesVisible = $var;

        return $this;
    }

    /**
     * Get isChosenMenteesVisible
     *
     * @return bool
     */
    public function getIsChosenMenteesVisible()
    {
        return $this->isChosenMenteesVisible;
    }

    /**
     * Set isUsersAccountsEnabled
     *
     * @param boolean $isUsersAccountsEnabled
     *
     * @return Config
     */
    public function setIsUsersAccountsEnabled($var)
    {
        $this->isUsersAccountsEnabled = $var;

        return $this;
    }

    /**
     * Get isUsersAccountsEnabled
     *
     * @return bool
     */
    public function getIsUsersAccountsEnabled()
    {
        return $this->isUsersAccountsEnabled;
    }

    public function setOrganization($var)
    {
        $this->organization = $var;
    }

    /**
     * Frontend needs that config
     * @return integer
     */
    public function getNumberOfEnabled()
    {
        $enabled = [
            $this->isSignupMenteesEnabled,
            $this->isSignupMentorsEnabled,
            $this->isSignupPartnersEnabled
        ];

        return count(array_filter($enabled));
    }
}

