<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Entity\Config;

class ConfigType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'isSignupMentorsEnabled', CheckboxType::class, [
                'label'    => 'config.signup.isSignupMentorsEnabled',
                'required' => false,
                ]
            )
            ->add(
                'isSignupPartnersEnabled', CheckboxType::class, [
                'label'    => 'config.signup.isSignupPartnersEnabled',
                'required' => false,
                ]
            )
            ->add(
                'isSignupMenteesEnabled', CheckboxType::class, [
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

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Config::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'signup_config';
    }


}
