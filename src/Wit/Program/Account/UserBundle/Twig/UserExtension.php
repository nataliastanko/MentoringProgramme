<?php

namespace Wit\Program\Account\UserBundle\Twig;

class UserExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return  [
            new \Twig_SimpleFilter('humanReadableRole', array($this, 'humanReadableRole')),
        ];
    }

    // public function getFunctions()
    // {
    //     return [ // Twig_SimpleFunction
    //         'humanReadableRole' => new \Twig_Function_Method($this, 'humanReadableRole'),
    //     ];
    // }

    public function humanReadableRole($role)
    {
        return str_replace('_', ' ', str_replace('ROLE', '', $role));
    }

    /**
     * Prior to Twig 1.26, your extension had to define an additional getName() method
     * that returned a string with the extension's internal name
     * (e.g. app.my_extension)
     *
     * @return string
     */
    public function getName()
    {
        return 'user_extension';
    }
}
