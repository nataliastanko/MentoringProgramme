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
        return [
            FormEvents::SUBMIT => 'onSubmit',
        ];
    }

    public function onSubmit(FormEvent $event)
    {
        $form = $event->getForm();

        /**
         * Temporary fix
         * @todo replace with kernel.event_subscriber
         * and check for the correct form
         * @see https://stackoverflow.com/questions/36111439/symfony-access-entity-manager-inside-eventsubscriber
         * example
         */
        if (!$form->isRoot()) {
            $form = $form->getRoot();
            if (!$form->has('organization')) {
                $entity = $form->getData();
                $organization = $this->subdomainDetection->getOrganization();
                $entity->setOrganization($organization);
            }
        }
    }
}
