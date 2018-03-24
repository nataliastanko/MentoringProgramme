<?php

namespace SiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Entity\Mentor;
use AdminBundle\Form\AnswerType;

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
                    'label' => 'person.name',
                ]
            )
            ->add(
                'lastName', TextType::class, [
                    'label' => 'person.lastName',
                ]
            )
            ->add(
                'company', TextType::class, [
                    'label' => 'company.name',
                    'required' => false,
                ]
            )
            ->add(
                'email', EmailType::class, [
                    'label' => 'email',
                ]
            )
            ->add(
                'answers', CollectionType::class, [
                    'entry_type' => AnswerType::class,
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
                'statute', CheckboxType::class, [
                    'label' => 'person.statute',
                    'required' => true,
                    'mapped' => false,
                ]
            )
            ->add(
                'rules', CheckboxType::class, [
                    'label' => 'mentor.rules', // she or he translation
                    'required' => true,
                    'mapped' => false,
                ]
            )
            // bio is translatable
            ->add(
                'translations', TranslationsType::class, [
                    'locales' => ['pl', 'en'], // @FIXME hardcoded locale list
                    'required' => false, // backend validation - atLeastOneBio
                    // 'required_locales' => ['pl'],
                    'label' => 'form.translations.self',
                    // 'error_bubbling' => true,
                    'fields' => [
                        'bio' => [
                            'field_type' => TextareaType::class,
                            'label' => 'mentor.bio',
                            'attr' => [
                                'rows' => '10'
                            ],
                        ],
                    ],
                    'exclude_fields' => ['occupation'],
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
            'validation_groups' => ['mentor_apply'],
            'honeypot' => true, // spam prevention
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mentor_apply';
    }
}
