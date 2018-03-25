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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Entity\Mentor;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class MentorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name', TextType::class, [
                    'label' => 'mentor.name',
                ]
            )
            ->add(
                'lastName', TextType::class, [
                    'label' => 'mentor.lastName',
                ]
            )
            ->add(
                'email', EmailType::class, [
                    'label' => 'email',
                ]
            )
            ->add(
                'url', UrlType::class, [
                    'label' => 'mentor.url',
                    'required' => false,
                ]
            )
            ->add(
                'personalUrl', UrlType::class, [
                    'label' => 'mentor.personalUrl',
                    'required' => false,
                ]
            )
            ->add(
                'company', TextType::class, [
                    'label' => 'company.name',
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
                'photoFile', VichImageType::class,
                [
                'label' => 'photo',
                'required' => false,
                'allow_delete' => true, // not mandatory, default is true
                'download_link' => true, // not mandatory, default is true
                ]
            )
            // bio is translatable
            ->add(
                'translations', TranslationsType::class, [
                    'required' => false, // backend validation - atLeastOneBio
                    'label' => 'form.translations.self',
                    // 'error_bubbling' => true,
                    'fields' => [
                        'bio' => [
                            'field_type' => TextareaType::class,
                            'label' => 'mentor.bio',
                            'attr' => [
                                'rows' => '10'
                            ],
                            'error_bubbling' => true // display errors on top of the form (in parent)
                        ],
                        'occupation' => [  // only admin can edit this field
                            'field_type' => TextareaType::class,
                            'label' => 'mentor.occupation',
                            'attr' => [
                                'rows' => '3'
                            ],
                            'error_bubbling' => true // display errors on top of the form (in parent)
                        ],
                    ],
                ]
            )
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mentor::class,
            'validation_groups' => array('settings'),
            // 'error_bubbling' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mentor_new';
    }
}
