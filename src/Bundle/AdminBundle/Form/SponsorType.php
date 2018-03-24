<?php

namespace AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class SponsorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name', TextType::class, [
                    'label' => 'sponsor.name',
                ]
            )
            ->add(
                'email', EmailType::class, [
                    'label' => 'email',
                    'required' => false,
                ]
            )
            ->add(
                'url', UrlType::class, [
                    'label' => 'url',
                    'required' => false,
                ]
            )
            // http://symfony.com/doc/3.0/reference/forms/types/entity.html
            ->add(
                'editions', EntityType::class,
                [
                'required' => true,
                'label' => 'edition',
                'class' => 'Entity:Edition',
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) {
                    // optional
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
                ]
            )
            ->add(
                'description', TextareaType::class, [
                    'label' => 'sponsor.description',
                    'required' => false,
                    'attr' => [
                        'rows' => '10'
                    ],
                ]
            )
            ->add(
                'photoFile', VichImageType::class,
                [
                'label' => 'photo',
                'required' => false,
                'allow_delete' => true, // not mandatory, default is true
                'download_link' => true, // not mandatory, default is true
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'Entity\Sponsor',
            'validation_groups' => array('settings'),
            )
        );
    }
}
