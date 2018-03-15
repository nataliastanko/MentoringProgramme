<?php

namespace Wit\Program\CalendarBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use SC\DatetimepickerBundle\Form\Type\DatetimeType;

/**
 * @TODO brute force zabezpieczenie
 */
class EventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
         */
        $ac = $options['ac'];
        $editionId = $options['editionId'];
        $user = $options['user'];

        $builder
            ->add(
              'startTime', DatetimeType::class, [
                'label' => 'event.startTime',
                'pickerOptions' => [
                  'format' => 'dd-mm-yyyy hh:ii',
                  'autoclose' => true,
                  'todayHighlight' => true,
                  'keyboardNavigation' => true,
                  // 'language' => 'en',
                  'minuteStep' => 15,
                  'todayBtn' => true,
                  'weekStart' => 1, // Monday
                ],
              ]
            )
            ->add(
              'endTime', DatetimeType::class, [
              // http://www.malot.fr/bootstrap-datetimepicker/#options
                'label' => 'event.endTime',
                'pickerOptions' => [
                  'format' => 'dd-mm-yyyy hh:ii',
                  'weekStart' => 1, // Monday
          //         'startDate' => date('m/d/Y'), //example
          //         'endDate' => '01/01/3000', //example
          //         'daysOfWeekDisabled' => '0,6', //example
                  'autoclose' => true,
          //         'startView' => 'month',
          //         'minView' => 'hour',
          //         'maxView' => 'decade',
                  'todayBtn' => false,
                  'todayHighlight' => true,
                  'keyboardNavigation' => true,
          //         'language' => 'en',
          //         'forceParse' => true,
                  'minuteStep' => 15,
          //         'pickerReferer ' => 'default', //deprecated
          //         'pickerPosition' => 'bottom-right',
          //         'viewSelect' => 'hour',
          //         'showMeridian' => false,
          //         'initialDate' => date('m/d/Y', 1577836800), //example
                ],
              ]
            )
            ->add(
                'title', TextType::class, [
                    'label' => 'event.title',
                ]
            )
            ->add(
                'description', TextareaType::class, [
                    'label' => 'event.description',
                    'attr' => [
                      'rows' => '10',
                    ],
                ]
            )
            ;

            $builder->addEventListener(
            FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($ac, $user, $editionId) {

                $form = $event->getForm();

                if ($ac->isGranted('ROLE_MENTOR')) {
                    $participantType = 'mentee';
                    $mentee = $user->getInvitation()->getMentor()->findChosenPerson();

                    if (!$mentee) { // mentor did not yet choose a mentee
                        return;
                    }

                    $invitation = $mentee->getInvitation();
                    if (
                        !$invitation // nie wysłano zaproszenia
                        ||
                        !$invitation->getIsAccepted() // nie jest jeszcze zarejestrowana w systemie
                    ) {
                        $form->add(
                            'participant', ChoiceType::class, [
                              'label' => 'event.participants',
                                'choices' => [
                                    $mentee->getFullname() => true
                                ],
                                'mapped' => false,
                                'attr' => ['readonly' => true],
                            ]
                        );
                        return;
                    }
                } else if ($ac->isGranted('ROLE_MENTEE')) {
                    $participantType = 'mentor';
                    $mentor = $user->getInvitation()->getPerson()->getMentor();
                    $invitation = $mentor->getInvitation();
                    if (
                        !$invitation // nie wysłano zaproszenia
                        ||
                        !$invitation->getIsAccepted() // nie jest jeszcze zarejestrowana w systemie
                    ) {
                        $form->add(
                            'participant', ChoiceType::class, [
                              'label' => 'event.participants',
                                'choices' => [
                                    $mentor->getFullname() => true
                                ],
                                'mapped' => false,
                                'attr' => ['readonly' => true],
                            ]
                        );
                        return;
                    }
                } else {
                    // admin and others
                    return;
                }

                $invitationId = $invitation->getId();

                // if user exists add participants to choose from
                $form->add(
                    'participant', EntityType::class,
                    [
                    'label' => 'event.participants',
                    'attr' => ['readonly' => true],
                    'required' => true,
                    'choice_label' => 'fullname', // choice_label is unique to Symfony3
                    'group_by' => 'roleName',
                    'class' => 'WitProgramAccountUserBundle:User',
                    // 'expanded' => true, // group_by does not work with expanded
                    // 'multiple' => true,
                    'query_builder' =>
                        function (EntityRepository $er) use ($editionId, $invitationId, $participantType) {
                            return $er->getEventParticipantsQuery($editionId, $invitationId, $participantType);
                        },
                    ]
                )
                ;
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                array(
                'data_class' => 'Wit\Program\CalendarBundle\Entity\Event',
                )
            )
            ->setRequired(
                [
                    'editionId',
                    'ac',
                    'user',
                ]
            )
        ;
    }
}
