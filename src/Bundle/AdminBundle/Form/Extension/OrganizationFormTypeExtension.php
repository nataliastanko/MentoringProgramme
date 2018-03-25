<?php

namespace AdminBundle\Form\Extension;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use SiteBundle\Service\SubdomainDetection;
use AdminBundle\Form\Subscriber\OrganizationSubscriber;

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

    public function getName()
    {
        return 'extend_translatable';
    }
}
