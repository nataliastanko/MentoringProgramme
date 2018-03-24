<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Entity\Config;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class ConfigType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var array */
        $sectionsEnabled = $options['sectionsEnabled'];

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($sectionsEnabled) {

                $form = $event->getForm();

                /* @var Config */
                $config = $event->getData();

                if (array_key_exists('partners', $sectionsEnabled)) {
                    $form->add(
                        'isSignupPartnersEnabled', CheckboxType::class,
                        [
                            'label'    => 'config.signup.isSignupPartnersEnabled',
                            'required' => false,
                        ]
                    );
                }

                if (array_key_exists('mentors', $sectionsEnabled)) {
                    $form->add(
                        'isSignupMentorsEnabled', CheckboxType::class,
                        [
                            'label'    => 'config.signup.isSignupMentorsEnabled',
                            'required' => false,
                        ]
                    );
                }

                if (array_key_exists('mentees', $sectionsEnabled)) {
                    $form
                        ->add(
                        'isSignupMenteesEnabled', CheckboxType::class,
                        [
                            'label'    => 'config.signup.isSignupMenteesEnabled',
                            'required' => false,
                        ]
                        )
                        ->add(
                            'isChosenMenteesVisible', CheckboxType::class, [
                            'label'    => 'config.signup.isChosenMenteesVisible',
                            'required' => false,
                            ]
                        )
                    ;
                }
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
                    'data_class' => Config::class,
                ]
            )
            ->setRequired('sectionsEnabled');
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'signup_config';
    }

}
