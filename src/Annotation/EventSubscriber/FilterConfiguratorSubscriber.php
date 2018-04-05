<?php

namespace Annotation\EventSubscriber;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Service\EventSubscriber\SubdomainAwareSubscriber;
use Entity\Organization;

/**
 * @author Michaël Perrin (http://blog.michaelperrin.fr)
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class FilterConfiguratorSubscriber implements EventSubscriberInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Reader */
    private $reader;

    /** @var SubdomainAwareSubscriber */
    private $subdomainDetection;

    public function __construct(EntityManagerInterface $em, Reader $reader, SubdomainAwareSubscriber $subdomainDetection)
    {
        $this->em = $em;
        $this->reader = $reader;
        $this->subdomainDetection = $subdomainDetection;
    }

    /**
     * @return array subscribed events, their methods and priorities
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                'configureAnnotationFilter', -10,
            ]
        ];
    }

    public function configureAnnotationFilter(GetResponseEvent $event)
    {
        if ($org = $this->subdomainDetection->getOrganization()) {
            $filter = $this->em->getFilters()->enable('organization_filter');
            // organization identification field
            $filter->setParameter('id', $org->getId());
            $filter->setAnnotationReader($this->reader);
        }
    }
}
