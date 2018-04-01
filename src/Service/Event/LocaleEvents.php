<?php

namespace Service\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class LocaleEvents extends Event
{
    /**
     * The locale.preferrence.changed event is dispatched each time a locale has been changed
     * @todo listen for _locale change from request
     */
    public const PREFERRENCE_CHANGE = 'locale.preferrence.changed';
}
