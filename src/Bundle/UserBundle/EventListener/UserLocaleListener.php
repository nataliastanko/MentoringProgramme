<?php

namespace UserBundle\EventListener;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Stores the locale of the user in the session after the
 * login. This can be used by the LocaleSubscriber afterwards.
 *
 * @link https://symfony.com/doc/current/session/locale_sticky_session.html
 *
 * update locale with fosu profile
 * @link https://stackoverflow.com/questions/16623409/symfony-2-set-locale-based-on-user-preferences-stored-in-db
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class UserLocaleListener implements EventSubscriberInterface
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Read user's locale preference and set to session
     * @param InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (null !== $user->getLocale()) {
            $this->session->set('_locale', $user->getLocale());
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        );
    }
}
