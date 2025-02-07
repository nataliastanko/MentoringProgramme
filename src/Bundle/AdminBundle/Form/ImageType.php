<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class ImageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'photoFile', VichImageType::class,
                [
                 'label' => 'photo',
                 'required' => true,
                 'allow_delete' => false, // not mandatory, default is true
                 'download_link' => true, // not mandatory, default is true
                 ]
            )
            ->add(
                'translations', TranslationsType::class, [
                    'required' => false,
                    'label' => 'form.translations.self',
                    'fields' => [
                        'description' => [
                            'field_type' => TextareaType::class,
                            'label' => 'image.description',
                            'attr' => [
                                'rows' => '10'
                            ],
                            // 'locale_options' => [
                            //     'en' => [
                            //         'label' => 'abc en'
                            //     ],
                            //     'pl' => [
                            //         'label' => 'abc pl'
                            //     ]
                            // ]
                        ],
                    ],
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
            'data_class' => 'Entity\Image',
            'validation_groups' => ['settings']
            )
        );
    }
}
