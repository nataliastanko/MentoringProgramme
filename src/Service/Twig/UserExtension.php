<?php

namespace Service\Twig;

/**
 * @see https://symfony.com/doc/3.4/templating/twig_extension.html
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class UserExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('humanReadableRole', array($this, 'humanReadableRole')),
        ];
    }

    public function humanReadableRole($role)
    {
        return str_replace('_', ' ', str_replace('ROLE', '', $role));
    }
}
