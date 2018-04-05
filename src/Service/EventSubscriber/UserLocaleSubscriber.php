<?php

namespace Service\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Service\Event\LocaleEvents;
use Service\Event\LocaleEvent;
use FOS\UserBundle\Doctrine\UserManager;

/**
 * On every login
 *
 * User`s locale stored in db
 * to be saved in session
 * and to be used by the LocaleSubscriber afterwards.
 *
 * @link https://symfony.com/doc/3.4/session/locale_sticky_session.html#setting-the-locale-based-on-the-user-s-preferences
 *
 * update locale with fosu profile
 * @link https://stackoverflow.com/questions/16623409/symfony-2-set-locale-based-on-user-preferences-stored-in-db
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class UserLocaleSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private $defaultLocale;

    /** @var array */
    private $possibleLocales;

    /** @var SessionInterface */
    private $session;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var UserManagerInterface */
    private $userManager;

    /** @var TokenStorage */
    private $userToken;

    public function __construct(SessionInterface $session, EventDispatcherInterface $ed, UserManager $um, TokenStorage $token, $defaultLocale, $possibleLocales)
    {
        $this->defaultLocale = $defaultLocale;
        $this->possibleLocales = $possibleLocales;
        $this->session = $session;
        $this->userToken = $token;
        $this->userManager = $um;
        $this->eventDispatcher = $ed;
    }

    public static function getSubscribedEvents()
    {
        return array(
            // priority must be higher than RouterListener::onKernelRequest() 32
            KernelEvents::REQUEST => ['listenForLocaleRoutingParam', 33],
            // priority must be higher than listenForLocaleRoutingParam
            SecurityEvents::INTERACTIVE_LOGIN => ['loadPreferredLocale', 34],
            LocaleEvents::PREFERRENCE_CHANGE => ['savePreferredLocale', 0],
        );
    }

    /**
     * Override locale from routing parameter
     * Make the locale sticky during logged user's session
     * @param  LocaleEvent $event
     * @return void
     */
    private function stickLocale($locale)
    {
        $this->session->set('_locale', $locale);
    }

    /**
     * Save user's preferrence
     * @param  LocaleEvent $event
     * @return void
     */
    public function savePreferredLocale(LocaleEvent $event)
    {
        $locale = $event->getLocale();

        // check if chosen locale in array of available translations
        if (in_array($locale, $this->possibleLocales)) {

            // update user's preferrence
            $user = $this->userToken->getToken()->getUser();
            $user->setLocale($locale);
            $this->userManager->updateUser($user);

            $this->stickLocale($locale);
        }
    }

    /**
     * Read user's locale preference
     * @param InteractiveLoginEvent $event
     * @return void
     */
    public function loadPreferredLocale(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        $locale = $user->getLocale();

        // check prefered user locale
        if (null !== $locale) {
            $this->stickLocale($locale);
        }
    }

    /**
     * During logged user's session
     * look for locale's change
     * @param  GetResponseEvent $event
     * @return void
     */
    public function listenForLocaleRoutingParam(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        // try to see if the locale has been set as a _locale routing parameter
        if ($locale = $request->attributes->get('_locale')) {
            $this->stickLocale($locale);
        } else {
            // if no explicit locale has been set on this request, use one from the session
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }

    }

}
