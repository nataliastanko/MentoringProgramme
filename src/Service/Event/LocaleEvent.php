<?php

namespace Service\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Locale event class
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class LocaleEvent extends Event
{
    /** @var string */
    private $locale;

    public function __construct($locale = '')
    {
        if ($locale) {
            $this->locale = $locale;
        }
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale() : string
    {
        return $this->locale;
    }
}
