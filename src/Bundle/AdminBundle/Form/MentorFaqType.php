<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;

class MentorFaqType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'translations', TranslationsType::class, [
                    'label' => 'form.translations.self',
                    'locales' => ['pl', 'en'],
                    'required_locales' => ['pl'],
                    'fields' => [
                        'question' => [
                            'field_type' => TextareaType::class,
                            'label' => 'faq.question',
                        ],
                        'answer' => [
                            'field_type' => TextareaType::class,
                            'label' => 'faq.answer',
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
            'data_class' => 'Entity\MentorFaq',
            ]
        );
    }
}
