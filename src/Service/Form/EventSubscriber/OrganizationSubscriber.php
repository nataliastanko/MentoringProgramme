<?php

namespace Service\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Service\EventSubscriber\SubdomainAwareSubscriber;

/**
 * Form sevent ubscriber
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class OrganizationSubscriber implements EventSubscriberInterface
{
    /** @var SubdomainAwareSubscriber */
    private $subdomainDetection;

    public function __construct(SubdomainAwareSubscriber $subdomainDetection)
    {
        $this->subdomainDetection = $subdomainDetection;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the onSubmit
        // event and that the onSubmit method should be called.
        return [
            FormEvents::SUBMIT => 'onSubmit',
        ];
    }

    public function onSubmit(FormEvent $event)
    {
        $form = $event->getForm();

        $entity = $form->getData();

        if (is_object($entity)) {
            $organization = $this->subdomainDetection->getOrganization();
            $entity->setOrganization($organization);
        }

    }
}
