<?php

namespace AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class OrganizationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var array */
        $sectionsEnabled = $options['sectionsEnabled'];

        $builder
            ->add(
                'name', TextType::class, [
                    'label' => 'name',
                ]
            )
            ->add(
                'contactEmail', EmailType::class, [
                    'label' => 'organization.email.contact',
                    'required' => false
                ]
            )
            ;

            $builder->add(
                'url', UrlType::class, [
                    'label' => 'url',
                    'required' => false,
                ]
            )
            ->add(
                'fbUrl', UrlType::class, [
                    'label' => 'Facebook page',
                    'required' => false,
                ]
            )
            ->add(
                'description', TextareaType::class, [
                    'label' => 'description',
                    'required' => false,
                    'attr' => [
                        'rows' => '10'
                    ],
                ]
            )
            ->add(
                'logoFile', VichImageType::class,
                [
                'label' => 'Logo',
                'required' => false,
                'allow_delete' => true, // not mandatory, default is true
                'download_link' => true, // not mandatory, default is true
                ]
            )
            ->add(
                'partnersEmail', EmailType::class, [
                    'label' => 'organization.email.partners',
                    'required' => false
                ]
            );

            $builder->addEventListener(
                FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($sectionsEnabled) {

                    $form = $event->getForm();

                    if (array_key_exists('menteesExternalSignup', $sectionsEnabled)) {
                        $form->add(
                            'menteesExternalSignupUrl', UrlType::class, [
                                'label' => 'organization.menteesExternalSignupUrl',
                                'required' => false
                            ]
                        );
                    }
                }
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'data_class' => 'Entity\Organization',
                    'validation_groups' => ['settings'],
                ]
            )
            ->setRequired('sectionsEnabled');
        ;
    }
}
