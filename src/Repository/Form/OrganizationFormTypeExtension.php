<?php

namespace Repository\Form;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use SiteBundle\Service\SubdomainDetection;
use Repository\Form\OrganizationSubscriber;

/**
 * Form Type Extension
 * Add a generic feature to several types
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class OrganizationFormTypeExtension extends AbstractTypeExtension
{
    /** @var SubdomainDetection */
    private $subdomainDetection;

    public function __construct(SubdomainDetection $subdomainDetection)
    {
        $this->subdomainDetection = $subdomainDetection;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new OrganizationSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        // Symfony\Component\Form\Extension\Core\Type\FormType
        return FormType::class;
    }
}
