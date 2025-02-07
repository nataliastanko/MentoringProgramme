<?php

namespace Service\EventSubscriber;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Service\EventSubscriber\SubdomainAwareSubscriber;

/**
 * Dotrine 2 filter based on a relation with organization
 * @author webdevilopers (https://gist.github.com/webdevilopers/461ff76b45046137f968)
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class OrganizationSubscriber implements EventSubscriber
{
    /** @var Reader */
    private $reader;

    /** @var SubdomainAwareSubscriber */
    private $subdomainDetection;

    public function __construct(Reader $reader, SubdomainAwareSubscriber $subdomainDetection)
    {
        $this->reader = $reader;
        $this->subdomainDetection = $subdomainDetection;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist => 'prePersist',
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        // the doctrine filter is called for any query on any entity
        // check if the current entity is @OrganizationAware
        $object = new \ReflectionClass($entity);
        $organizationAware = $this->reader->getClassAnnotation(
            $object,
            'Repository\\Annotation\\OrganizationAware'
        );

        if ($organizationAware) {
            $organization = $this->subdomainDetection->getOrganization();
            $entity->setOrganization($organization);
        }
    }
}
