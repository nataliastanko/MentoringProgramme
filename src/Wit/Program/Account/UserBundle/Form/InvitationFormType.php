<?php

namespace Wit\Program\Account\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Wit\Program\Account\UserBundle\Form\DataTransformer\InvitationToCodeTransformer;

/**
 * Create the invitation field.
 * @link https://symfony.com/doc/current/bundles/FOSUserBundle/adding_invitation_registration.html
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
            'class' => 'Wit\Program\Account\UserBundle\Entity\Invitation',
            'required' => true,
        ));
    }

    /**
     * Hidden form field
     * @return [type] [description]
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\HiddenType';

        // Or for Symfony < 2.8
        // return 'text'; // hidden
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
