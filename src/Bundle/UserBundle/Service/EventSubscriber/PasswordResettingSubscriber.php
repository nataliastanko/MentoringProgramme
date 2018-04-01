<?php

namespace UserBundle\Service\EventSubscriber;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * fosuser events controller hook - resetting password
 * @todo add form protection against brute force register
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class PasswordResettingSubscriber implements EventSubscriberInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param UrlGeneratorInterface $session
     * @param Session $session
     * @param ValidatorInterface $session
     */
    public function __construct(UrlGeneratorInterface $router, Session $session, ValidatorInterface $validator) {
        $this->router = $router;
        $this->session = $session;
        $this->validator = $validator;
    }

    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE => 'beginResettingProcess',
        ];
    }

    /**
     * @param  GetResponseNullableUserEvent $event
     * @return void
     */
    public function beginResettingProcess(GetResponseNullableUserEvent $event)
    {
        $email = $event->getRequest()->get('username');

        $emailConstraint = new Assert\Email();
        // all constraint "options" can be set this way
        $emailConstraint->message = 'Invalid email address';

        // use the validator to validate the value
        $errorList = $this->validator->validate(
            $email,
            $emailConstraint
        );

        if (count($errorList)) {
            // this is *not* a valid email address
            $errorMessage = $errorList[0]->getMessage();

            $url = $this->router->generate('fos_user_resetting_request');
            $response = new RedirectResponse($url);

            // Add flash message
            $this->session->getFlashBag()
                ->add('warning', $errorMessage);

            $event->setResponse($response);
            return;
        }

        if (!$event->getUser()) {
            $url = $this->router->generate('fos_user_resetting_request');
            $response = new RedirectResponse($url);

            // Add flash message
            /** @todo add translation */
            $this->session->getFlashBag()
                ->add('warning', 'There is no such email adress like "'.$email.'" in our system. Are you sure this is the email you registered with?');

            $event->setResponse($response);
            return;
        }
    }

}
