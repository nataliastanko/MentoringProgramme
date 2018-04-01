<?php

namespace Service\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Service\EventSubscriber\SubdomainAwareSubscriber;
use Service\Form\EventSubscriber\OrganizationSubscriber;

/**
 * Form Type Extension
 * Add a generic feature to several types
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class OrganizationFormTypeExtension extends AbstractTypeExtension
{
    /** @var SubdomainAwareSubscriber */
    private $subdomainDetection;

    public function __construct(SubdomainAwareSubscriber $subdomainDetection)
    {
        $this->subdomainDetection = $subdomainDetection;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Add organization subscriber to translatable forms only
        $formName = $builder->getForm()->getName();

        // underscored short class name, (e.g. "UserProfileType" => "user_profile")
        $translatableFormsWithOrganization = [
            'rule',
            'about',
            'faq',
            'mentor_faq',
            'mentor',
            'person',
            'question'
        ];

        if (in_array($formName, $translatableFormsWithOrganization)) {
            $builder->addEventSubscriber(new OrganizationSubscriber($this->subdomainDetection));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}
