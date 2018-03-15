<?php

namespace Wit\Program\Admin\EditionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Config
 *
 * @ORM\Table(name="config")
 * @ORM\Entity(repositoryClass="Wit\Program\Admin\EditionBundle\Repository\ConfigRepository")
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

