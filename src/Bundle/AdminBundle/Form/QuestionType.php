<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Entity\Question;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class QuestionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                    'choices'  => [
                        'question.type.mentee' => Question::TYPE_MENTEE,
                        'question.type.mentor' => Question::TYPE_MENTOR,
                    ],
                    'label' => 'question.type.self',
                ]
            )
            ->add(
                'answerChoices', CollectionType::class, [
                    'label' => ' ', // do not display label
                    // 'entry_options' => ['label' => false], // try this instead of label ' '
                    'entry_type' => AnswerChoiceType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false, // call addTag()
                ]
            )
            ->add(
                'isVisibleForMentor', CheckboxType::class, [
                'label'    => 'question.isVisibleForMentor',
                'required' => false,
                ]
            )
            ->add(
                'translations', TranslationsType::class, [
                    'required' => false,
                    'label' => 'form.translations.self',
                    'fields' => [
                        'name' => [
                            'field_type' => TextType::class,
                            'label' => 'question.name',
                            'required' => false,
                        ],
                        'helpblock' => [
                            'field_type' => TextType::class,
                            'label' => 'question.description',
                            'required' => false,
                        ],
                    ],
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
            'validation_groups' => array('settings'),

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'question_new';
    }
}
