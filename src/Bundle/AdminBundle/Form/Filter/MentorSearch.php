<?php

namespace AdminBundle\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
// use Symfony\Component\Form\Extension\Core\Type\TextType;
// use Symfony\Bridge\Doctrine\Form\Type\EntityType;
// use Entity\Mentor;
use Petkopara\MultiSearchBundle\Form\Type\MultiSearchType;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class MentorSearch extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search', MultiSearchType::class, [
                'required' => true,
                'label' => false,
                'class' => 'Entity:Mentor', //required
                //optional, if it's empty it will search in the all entity columns
                'search_fields' => [
                    'name',
                    'lastName',
                    'email'
                 ],
                 //optional, what type of comparison to applied ('wildcard','starts_with', 'ends_with', 'equals')
                 'search_comparison_type' => 'wildcard',
                 'attr' => [
                    'placeholder' => 'name, last name or email' // @TODO translation
                ]
            ]
        )
        ;

    }

    // /**
    //  * {@inheritdoc}
    //  */
    // public function configureOptions(OptionsResolver $resolver)
    // {
    //     $resolver->setDefaults([
    //         'data_class' => Mentor::class,
    //         'validation_groups' => array('settings'),

    //     ]);
    // }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mentor_search';
    }
}
