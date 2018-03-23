<?php

namespace Repository\Filter;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\Reader;
use Entity\Organization;
use SiteBundle\Service\SubdomainDetection;

/**
 * @author Michaël Perrin (http://blog.michaelperrin.fr)
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class Configurator
{
    /** @var EntityManager */
    private $em;

    /** @var Reader */
    private $reader;

    /** @var SubdomainDetection */
    private $subdomainDetection;

    public function __construct(EntityManager $em, Reader $reader, SubdomainDetection $subdomainDetection)
    {
        $this->em = $em;
        $this->reader = $reader;
        $this->subdomainDetection = $subdomainDetection;
    }

    public function onKernelRequest()
    {
        if ($org = $this->subdomainDetection->getOrganization()) {
            $filter = $this->em->getFilters()->enable('organization_filter');
            // organization identification field
            $filter->setParameter('id', $org->getId());
            $filter->setAnnotationReader($this->reader);
        }
    }
}
