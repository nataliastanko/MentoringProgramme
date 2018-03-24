<?php

namespace Repository\Form\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use SiteBundle\Service\SubdomainDetection;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class OrganizationSubscriber implements EventSubscriberInterface
{
    /** @var SubdomainDetection */
    private $subdomainDetection;

    public function __construct(SubdomainDetection $subdomainDetection)
    {
        $this->subdomainDetection = $subdomainDetection;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(
            // FormEvents::PRE_SUBMIT   => 'preSubmit',
            FormEvents::PRE_SUBMIT   => 'onPreSubmit',
        );
    }

    public function onPreSubmit(FormEvent $event)
    {
        $entity = $event->getData();
        $form = $event->getForm();

        $organization = $this->subdomainDetection->getOrganization();
        $entity->setOrganization($organization);

    }
}
