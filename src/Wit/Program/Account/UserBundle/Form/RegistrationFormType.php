<?php

namespace Wit\Program\Account\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use FOS\UserBundle\Form\Type\RegistrationFormType as FosRegistrationFormType;
use FOS\UserBundle\Model\UserManager;
use Wit\Program\Account\UserBundle\Entity\Invitation;

/**
 * Override the default registration form with your own.
 * @link https://symfony.com/doc/current/bundles/FOSUserBundle/adding_invitation_registration.html
 */
class RegistrationFormType extends FosRegistrationFormType
// class RegistrationFormType extends AbstractType
{
    protected $userManager;
    protected $requestStack;

    // Add constructor with UserManager
    public function __construct($class, UserManager $userManager, RequestStack $requestStack)
    {
        parent::__construct($class);
        $this->userManager = $userManager;
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // @TODO disable unused services from invitation first implementation
        $builder
            // Or for Symfony < 2.8
            // ->add('invitation', 'program_invitation_type')
            ->add('invitation', 'Wit\Program\Account\UserBundle\Form\InvitationFormType')
            // https://stackoverflow.com/questions/8832916/remove-replace-the-username-field-with-email-using-fosuserbundle-in-symfony2/21064627#21064627
            ->remove('username')
        ;

        // Add hook on submitted data in order to copy email into username
        $um = $this->userManager;

        $request = $this->requestStack->getCurrentRequest();
        // $_GET parameters
        $code = $request->query->get('code');

        // http://api.symfony.com/3.3/Symfony/Component/Form/FormEvents.html

        /**
         * The FormEvents::PRESETDATA event is dispatched at the beginning of the Form::setData() method.
         * It can be used to:
         * - Modify the data given during pre-population;
         * - Modify a form depending on the pre-populated data (adding or removing fields dynamically).
         * @var [type]
         */
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($code) {

                /** @var $user \Symfony\Component\Form\Form */
                $form = $event->getForm();
                // $form->get('email')->setData('@');

                $invitation = new Invitation($code);
                // $invitation->setCode($code);
                $form->get('invitation')->setData($invitation);
            }
        );

        /**
         * The SUBMIT event is dispatched just before the Form::submit() method transforms back the normalized data to the model and view data.
         * It can be used to change data from the normalized representation of the data.
         * @var [type]
         */
        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) use ($um) {

                /** @var $user \FOS\UserBundle\Model\UserInterface */
                $user = $event->getData();
                if ($user) {
                    // Set username like email and canonicalize both fields
                    $user->setUsername($user->getEmail());
                    $um->updateCanonicalFields($user);
                }
            }
        );
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }

    public function getBlockPrefix()
    {
        return 'program_user_registration';
    }

    // Not necessary on Symfony 3+
    public function getName()
    {
        return 'program_user_registration';
    }
}
