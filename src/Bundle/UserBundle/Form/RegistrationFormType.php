<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use FOS\UserBundle\Form\Type\RegistrationFormType as FosRegistrationFormType;
use FOS\UserBundle\Model\UserManager;
use Entity\Invitation;

/**
 * Override the default registration form with your own.
 * @TODO disable unused services from invitation first implementation
 *
 * @link https://symfony.com/doc/current/bundles/FOSUserBundle/adding_invitation_registration.html
 * @see https://stackoverflow.com/questions/8832916/remove-replace-the-username-field-with-email-using-fosuserbundle-in-symfony2/21064627#21064627
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class RegistrationFormType extends FosRegistrationFormType
{
    /** @var UserManager */
    protected $userManager;

    /** @var RequestStack */
    protected $requestStack;

    /**
     * Add constructor with UserManager
     * @param $class
     * @param UserManager  $userManager
     * @param RequestStack $requestStack we need invite code
     */
    public function __construct($class, UserManager $userManager, RequestStack $requestStack)
    {
        parent::__construct($class);
        $this->userManager = $userManager;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('invitation', 'UserBundle\Form\InvitationFormType')
            ->remove('username')
        ;

        // Add hook on submitted data in order to copy email into username
        $um = $this->userManager;

        $request = $this->requestStack->getCurrentRequest();
        // $_GET parameters
        $code = $request->query->get('code');

        /**
         * The FormEvents::PRESETDATA event is dispatched at the beginning of the Form::setData() method.
         * It can be used to:
         * - Modify the data given during pre-population;
         * - Modify a form depending on the pre-populated data (adding or removing fields dynamically).
         *
         * @see http://api.symfony.com/3.3/Symfony/Component/Form/FormEvents.html
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
         *
         * @see http://api.symfony.com/3.3/Symfony/Component/Form/FormEvents.html
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
    }

    public function getBlockPrefix()
    {
        return 'overrides_user_registration';
    }

    // Not necessary on Symfony 3+
    public function getName()
    {
        return 'overrides_user_registration';
    }
}
