<?php

namespace Wit\Program\Admin\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
// use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;

class AnswerChoiceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('name', TextType::class, [
            //         'label' => 'choice.name',
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
                            'label' => 'choice.name',
                            'required' => false,
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
            'data_class' => 'Wit\Program\Admin\QuestionnaireBundle\Entity\AnswerChoice',
            )
        );
    }
}
