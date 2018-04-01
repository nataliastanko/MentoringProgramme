<?php

namespace Service\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Service\EventSubscriber\SubdomainAwareSubscriber;

class EditionExtension extends \Twig_Extension
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var SubdomainAwareSubscriber */
    private $subdomainDetection;

    /** @var array */
    private $buttonsSectionsEnabled;

    public function __construct(EntityManagerInterface $em, SubdomainAwareSubscriber $subdomainDetection)
    {
        $this->em = $em;
        $this->subdomainDetection = $subdomainDetection;
        $this->buttonsSectionsEnabled = [];
    }

    public function getFunctions()
    {
        return [
            'getEditions' => new \Twig_SimpleFunction('getEditions', array($this, 'getEditions')),
            'getLastEdition' => new \Twig_SimpleFunction('getLastEdition', array($this, 'getLastEdition')),
            'getSignupConfig' => new \Twig_SimpleFunction('getSignupConfig', array($this, 'getSignupConfig')),
            'getEnabledButtons' => new \Twig_SimpleFunction('getEnabledButtons', array($this, 'getEnabledButtons')),
            'getSectionsEnabled' => new \Twig_SimpleFunction('getSectionsEnabled', array($this, 'getSectionsEnabled')),
            'getOrganization' => new \Twig_SimpleFunction('getOrganization', array($this, 'getOrganization')),
        ];
    }

    /**
     * For menu items
     * generate a route to every edition
     * @todo can be an Embed Controller
     * @see https://symfony.com/doc/3.4/templating/embedding_controllers.html
     * @return ArrayCollection
     * @return \Entity\Edition[]
     */
    public function getEditions()
    {
        return $this->em->getRepository('Entity:Edition')
            ->getBySortableGroupsQuery()->getResult();
    }

    /**
     * Find last edition
     * @return \Entity\Edition
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
     * @return array
     */
    public function getSectionsEnabled()
    {
        return $this->getOrganization()->getSectionsEnabledArray();
    }
}
