<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use UserBundle\Form\DataTransformer\InvitationToCodeTransformer;

/**
 * Create the invitation field.
 * @link https://symfony.com/doc/current/bundles/FOSUserBundle/adding_invitation_registration.html
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class InvitationFormType extends AbstractType
{
    private $invitationTransformer;

    public function __construct(InvitationToCodeTransformer $invitationTransformer)
    {
        $this->invitationTransformer = $invitationTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->invitationTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Entity\Invitation',
            'required' => true,
        ));
    }

    /**
     * Hidden form field
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\HiddenType';
    }

    public function getBlockPrefix()
    {
        return 'program_invitation_type';
    }

    // Not necessary on Symfony 3+
    public function getName()
    {
        return 'program_invitation_type';
    }
}
