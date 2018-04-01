<?php

namespace UserBundle\Service\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Doctrine\ORM\EntityManagerInterface;
use Entity\Invitation;

/**
 * fosuser events controller hook - registration
 * @todo add form protection against brute force register
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class RegistrationSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var AuthorizationChecker
     */
    private $ac;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param UrlGeneratorInterface $router
     * @param AuthorizationChecker  $ac
     * @param EntityManager         $em
     * @param Session               $session
     */
    public function __construct(
        UrlGeneratorInterface $router,
        AuthorizationChecker $ac,
        EntityManagerInterface $em,
        Session $session
    ) {
        $this->router = $router;
        $this->ac = $ac;
        $this->em = $em;
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialize',
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        ];
    }

    /**
     * 1) Prevent logged in people to register
     * https://symfony.com/blog/new-in-symfony-2-6-security-component-improvements
     * 2) Validate invitation before rendering registration form
     * @param GetResponseUserEvent $event
     */
    public function onRegistrationInitialize(GetResponseUserEvent $event)
    {
        // Prevent logged in people to register
        if ($this->ac->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $url = $this->router->generate('homepage');
            $response = new RedirectResponse($url);

            // Add flash message
            $this->session->getFlashBag()
                ->add('warning', 'You can not register - you are already logged in');

            $event->setResponse($response);
            return;
        }

        // Validate invitation
        $request = $event->getRequest();
        $code = $request->query->get('code');

        // no code parameter
        if (!$code && $request->isMethod('GET')) {
            $url = $this->router->generate('homepage');
            $response = new RedirectResponse($url);
            // Add flash message
            $this->session->getFlashBag()
                ->add('warning', 'Your invitation is invalid');

            $event->setResponse($response);
            return;
        }

        $invitation = $this->em->getRepository('Entity\Invitation')
            ->findOneBy(
                [
                    'code' => $code,
                    'isAccepted' => true
                ]
            );

        // already registered from that invitation
        if ($invitation) {
            $url = $this->router->generate('fos_user_security_login');
            $response = new RedirectResponse($url);

            // Add flash message
            $this->session->getFlashBag()
                ->add('warning', 'Your invitation expired or you already used it to register');

            $event->setResponse($response);
            return;
        }
    }

    /**
     * @param  FormEvent $event
     */
    public function onRegistrationSuccess(FormEvent $event)
    {
        // $code = $event->getRequest()->query->get('code');
        /** @var $user \FOS\UserBundle\Model\UserInterface */
        $user = $event->getForm()->getData();
        /** @var $user \Entity\Invitation */
        $invitation = $user->getInvitation();

        // set invitation as accepted
        $invitation->setIsAccepted(true);
        $this->em->persist($invitation);

        /** @var string */
        $role = $invitation->getRole();

        $user->addRole($role);

        // prepare profile depending on role
        if ($role === Invitation::ROLE_MENTOR) {

            $mentor = $invitation->getMentor();

            $user->setName($mentor->getName());
            $user->setLastName($mentor->getLastName());

            // is mentor registered
            if ($mentor->findChosenPerson()) {
                // assign events
                $mentee = $mentor->findChosenPerson();
                $menteeInvitation = $mentee->getInvitation();

                if ($menteeInvitation && $menteeInvitation->getIsAccepted()) {

                    $menteeUser = $this->em->getRepository('Entity:User')
                        ->findOneByInvitation($menteeInvitation->getId());

                    foreach ($menteeUser->getEventsCreated() as $e) {
                        if (!$e->getParticipant()) {
                            $e->setParticipant($user);
                            $this->em->persist($e);
                        }
                    }

                }
            }

        } else if ($role === Invitation::ROLE_MENTEE) {

            $person = $invitation->getPerson();

            $user->setName($person->getName());
            $user->setLastName($person->getLastName());

            // assign events
            $mentor = $person->getMentor();
            $mentorInvitation = $mentor->getInvitation();

            // is mentor registered
            if ($mentorInvitation && $mentorInvitation->getIsAccepted()) {

                $mentorUser = $this->em->getRepository('Entity:User')
                    ->findOneByInvitation($mentorInvitation->getId());

                foreach ($mentorUser->getEventsCreated() as $e) {
                    if (!$e->getParticipant()) {
                        $e->setParticipant($user);
                        $this->em->persist($e);
                    }
                }

            }

        }

        $url = $this->router->generate('account');
        $response = new RedirectResponse($url);
        $event->setResponse($response);
    }

}
