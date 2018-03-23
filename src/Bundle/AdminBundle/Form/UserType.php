<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserManager;
use Entity\Invitation;

class UserType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('lastName')
            ->add('email')
        ;

        /**
         * @var UserManager
         */
        $um = $options['um'];

        /**
         * @var UserManager
         */
        $em = $options['em'];

        /**
         * The SUBMIT event is dispatched just before the Form::submit() method transforms back the normalized data to the model and view data.
         * It can be used to change data from the normalized representation of the data.
         */
        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) use ($um, $em) {

                /** @var $user \FOS\UserBundle\Model\UserInterface */
                $user = $event->getData();
                if ($user) {
                    $email = $user->getEmail();
                    // Set username like email and canonicalize both fields
                    $user->setUsername($email);
                    $um->updateCanonicalFields($user);

                    // for mentor and person only

                    /**
                     * @var Invitation
                     */
                    if ($user->getInvitation()) {
                        if ($user->getInvitation()->getRole() == Invitation::ROLE_MENTEE) {
                            /**
                             * @var Persom
                             */
                            $person = $user->getInvitation()->getPerson();
                            $person->setEmail($email);
                            $em->persist($person);
                        } else if ($user->getInvitation()->getRole() == Invitation::ROLE_MENTOR) {
                            /**
                             * @var Mentor
                             */
                            $mentor = $user->getInvitation()->getMentor();
                            $mentor->setEmail($email);
                            $em->persist($mentor);
                        }
                    }
                }
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
                'data_class' => 'Entity\User',
                'validation_groups' => array('settings'),
                )
            )
            ->setRequired(
                [
                'em',
                'um'
                ]
            );
            ;
    }
}
