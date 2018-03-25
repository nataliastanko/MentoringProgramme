<?php

namespace AdminBundle\Twig;

use Doctrine\ORM\EntityManager;
use SiteBundle\Service\SubdomainDetection;

class EditionExtension extends \Twig_Extension
{
    private $em;

    /** @var SubdomainDetection */
    private $subdomainDetection;

    /** @var array */
    private $buttonsSectionsEnabled;

    public function __construct(EntityManager $em, SubdomainDetection $subdomainDetection)
    {
        $this->em = $em;
        $this->subdomainDetection = $subdomainDetection;
        $this->buttonsSectionsEnabled = [];
    }

    public function getFilters()
    {
        return [];
    }

    public function getFunctions()
    {
        return [ // Twig_SimpleFunction
            'getEditions' => new \Twig_SimpleFunction('getEditions', array($this, 'getEditions')),
            'getLastEdition' => new \Twig_SimpleFunction('getLastEdition', array($this, 'getLastEdition')),
            'getSignupConfig' => new \Twig_SimpleFunction('getSignupConfig', array($this, 'getSignupConfig')),
            'getEnabledButtons' => new \Twig_SimpleFunction('getEnabledButtons', array($this, 'getEnabledButtons')),
            'getSectionsEnabled' => new \Twig_SimpleFunction('getSectionsEnabled', array($this, 'getSectionsEnabled')),
            'getOrganization' => new \Twig_SimpleFunction('getOrganization', array($this, 'getOrganization')),
        ];
    }

    /**
     * For menu items (generate a route to a single edition)
     * @return ArrayCollection
     */
    public function getEditions()
    {
        return $this->em->getRepository('Entity:Edition')
            ->getBySortableGroupsQuery()->getResult();
    }

    /**
     * Find last edition
     * @return ArrayCollection
     */
    public function getLastEdition()
    {
        return $this->em->getRepository('Entity:Edition')
            ->findLastEdition();
    }

    /**
     * Get config setup to render signup buttons
     * @todo replace with organization entity
     * @return \Entity\Config|null
     */
    public function getSignupConfig()
    {
        return $this->em->getRepository('Entity:Config')->findConfig();
    }

    /**
     * Enabled button section list
     * @return array
     */
    public function getEnabledButtons()
    {
        return $this->em->getRepository('Entity:Config')->findConfig()->getEnabledButtons();
    }

    /**
     * @return \Entity\Organization
     */
    public function getOrganization()
    {
        return $this->subdomainDetection->getOrganization();
    }

    /**
     * Enabled sections
     */
    public function getSectionsEnabled()
    {
        return $this->getOrganization()->getSectionsEnabledArray();
    }

    public function getName()
    {
        return 'edition_extension';
    }
}
