<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
// use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class AnswerChoiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'translations', TranslationsType::class, [
                    'label' => 'form.translations.self',
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
            'data_class' => 'Entity\AnswerChoice',
            )
        );
    }
}
