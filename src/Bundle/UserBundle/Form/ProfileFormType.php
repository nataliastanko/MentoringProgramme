<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as FosProfileFormType;
use FOS\UserBundle\Model\UserManager;
use Entity\Invitation;

/**
 * Overwrite FOSu profile form
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class ProfileFormType extends FosProfileFormType
{
    /**
     * @var UserManager
     */
    protected $um;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    // Add constructor with UserManager
    public function __construct($class, UserManager $userManager, EntityManagerInterface $em)
    {
        parent::__construct($class);
        $this->um = $userManager;
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // https://stackoverflow.com/questions/8832916/remove-replace-the-username-field-with-email-using-fosuserbundle-in-symfony2/21064627#21064627
            ->remove('username')
        ;

        /**
         * The SUBMIT event is dispatched just before the Form::submit() method transforms back the normalized data to the model and view data.
         * It can be used to change data from the normalized representation of the data.
         * @var [type]
         */
        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {

                /** @var $user \FOS\UserBundle\Model\UserInterface */
                $user = $event->getData();
                if ($user) {
                    $email = $user->getEmail();
                    // Set username like email and canonicalize both fields
                    $user->setUsername($email);
                    $this->um->updateCanonicalFields($user);

                    // for mentor and person only
                    if ($user->getInvitation()) {
                        if ($user->getInvitation()->getRole() == Invitation::ROLE_MENTEE) {
                            $person = $user->getInvitation()->getPerson();
                            $person->setEmail($email);
                            $this->em->persist($person);
                        } else if ($user->getInvitation()->getRole() == Invitation::ROLE_MENTOR) {
                            $mentor = $user->getInvitation()->getMentor();
                            $mentor->setEmail($email);
                            $this->em->persist($mentor);
                        }
                    }
                }
            }
        );
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getBlockPrefix()
    {
        return 'overrides_user_profile';
    }

    // Not necessary on Symfony 3+
    public function getName()
    {
        return 'overrides_user_profile';
    }
}
