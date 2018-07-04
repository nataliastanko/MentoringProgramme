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
        ;
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
        ;
    }
}
