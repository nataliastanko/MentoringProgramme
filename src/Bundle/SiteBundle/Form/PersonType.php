<?php

namespace SiteBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvents;
use Entity\Person;
use AdminBundle\Form\AnswerType;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class PersonType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $editionId = $options['editionId'];

        $builder
            ->add(
                'name', TextType::class, [
                    'label' => 'person.name',
                ]
            )
            ->add(
                'lastName', TextType::class, [
                    'label' => 'lastname',
                ]
            )
            ->add(
                'email', EmailType::class, [
                    'label' => 'email',
                ]
            )
            ->add(
                'age', IntegerType::class, [
                    'label' => 'person.age',
                ]
            )
            ->add(
                'education', ChoiceType::class, [
                    'label' => 'person.education',
                    'placeholder' => 'choice.choose',
                    'choices' => Person::getEducationChoices(), // 'choices' => array('translated' => 'bd'),
                    'required' => true,
                    'choice_translation_domain' => true,
                ]
            )
            // ->add('edition', EntityType::class, [
            //         'label' => 'edition',
            //         'attr' => [
            //             'disabled' => true
            //         ],
            //         'class' => 'Entity:Edition',
            //         'choice_label' => 'name'
            //     ]
            // )
            ->add(
                'mentor', EntityType::class, [
                    'class' => 'Entity:Mentor',
                    'choice_label' => 'fullname',
                    'placeholder' => 'choice.choose',
                    'query_builder' => function (EntityRepository $er) use ($editionId) {
                        return $er->getFromEditionQuery($editionId, true);
                    },
                ]
            )
            ->add(
                'videoUrl', UrlType::class, [
                    'label' => 'mentee.url.self',
                    'required' => false,
                ]
            )
            ->add(
                'answers', CollectionType::class, [
                    'entry_type' => AnswerType::class,
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
                    'label' => 'person.rules',
                    'required' => true,
                    'mapped' => false,
                ]
            );

        // $builder->addEventListener(
        //     FormEvents::PRE_SUBMIT, function (FormEvent $event) {

        //         // @TODO set Edition for mentee here instead of in controller
        //         // change hidden edition field value
        //     }
        // );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'data_class' => Person::class,
                    'validation_groups' => array('add'),
                    'honeypot' => true, // spam prevention
                ]
            )
            ->setRequired('editionId');
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mentee_apply';
    }
}
