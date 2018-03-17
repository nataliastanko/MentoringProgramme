<?php

namespace Wit\Program\Admin\EditionBundle\Twig;

use Doctrine\ORM\EntityManager;

class EditionExtension extends \Twig_Extension
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
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
        ];
    }

    /**
     * For menu items (generate a route to a single edition)
     * @return ArrayCollection
     */
    public function getEditions()
    {
        return $this->em->getRepository('WitProgramAdminEditionBundle:Edition')
            ->getBySortableGroupsQuery()->getResult();
    }

    /**
     * Find last edition
     * @return ArrayCollection
     */
    public function getLastEdition()
    {
        return $this->em->getRepository('WitProgramAdminEditionBundle:Edition')
            ->findLastEdition();
    }

    /**
     * Get config setup to render signup buttons
     * @return Config|null
     */
    public function getSignupConfig()
    {
        return $this->em->getRepository('WitProgramAdminEditionBundle:Config')->findConfig();
    }

    public function getName()
    {
        return 'edition_extension';
    }
}
