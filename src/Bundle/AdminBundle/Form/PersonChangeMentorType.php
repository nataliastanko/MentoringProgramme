<?php

namespace AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Entity\Person;

class PersonChangeMentorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $editionId = $options['editionId'];

        $builder
            ->add(
                'mentor', EntityType::class, [
                    'class' => 'Entity:Mentor',
                    'choice_label' => 'fullname',
                    'placeholder' => 'choice.choose',
                    'query_builder' => function (EntityRepository $er) use ($editionId) {
                        // sort alpha 2nd param true
                        // return $er->getFromEditionQuery($editionId, true);
                        // sort alpha by default, chosen
                        return $er->findMentorsWithoutMenteesQuery($editionId, true);
                    },
                ]
            )
            ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA, function (FormEvent $event) {

                $form = $event->getForm();

                /* @var Person */
                $person = $event->getData();

                if (!$person->getOriginalMentorChoice()) {
                    $person->setOriginalMentorChoice($person->getMentor()->getFullname());
                }

                // set originalMentorChoice
                $form->add(
                        'originalMentorChoice', TextType::class, [
                            'label' => 'mentee.originalMentorChoice',
                            'attr' => [
                                'readonly' => true,
                            ]
                        ]
                    )
                ;
            }
        );

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
                    'validation_groups' => array('changeMentor'),
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
        return 'person_change_mentor';
    }
}
