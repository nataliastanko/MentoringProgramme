<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;

class AboutType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('name', TextType::class, [
            //         'label' => 'about.name',
            //         'required' => false
            //     ]
            // )
            // ->add('content', TextareaType::class, [
            //         'label' => 'about.content',
            //     ]
            // )
            ->add(
                'translations', TranslationsType::class, [
                    'label' => 'form.translations.self',
                    'locales' => ['pl', 'en'],
                    'required_locales' => ['pl'],
                    'fields' => [
                        'name' => [
                            'field_type' => TextType::class,
                            'label' => 'about.name',
                            'required' => false,
                        ],
                        'content' => [
                            'field_type' => TextareaType::class,
                            'label' => 'about.content',
                            'attr' => [
                                'rows' => '10'
                            ],
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
            [
            'data_class' => 'Entity\About',
            ]
        );
    }
}
