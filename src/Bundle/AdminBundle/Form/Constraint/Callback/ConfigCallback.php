<?php

namespace AdminBundle\Form\Constraint\Callback;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Entity\Config;

/**
 * @see https://symfony.com/doc/3.4/reference/constraints/Callback.html
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class ConfigCallback
{
    /**
     * To enable signup for partners
     * apply for partners email in organization details must be filled in
     *
     * @param  Config $object
     * @param  ExecutionContextInterface $context
     * @param  $payload
     * @return void
     */
    public static function checkPartnersEmail(Config $object, ExecutionContextInterface $context, $payload)
    {
        $organization = $object->getOrganization();
        $buttonsSectionsEnabled = $organization->getButtonsSectionsEnabledArray();

        // if partners section enabled
        if (isset($buttonsSectionsEnabled['partners']) && $object->getIsSignupPartnersEnabled()) {

            if (!$organization->getPartnersEmail()) {
                $context->buildViolation('config.partners.email.notBlank')
                    ->atPath('isSignupPartnersEnabled')
                    ->addViolation();
            }
        }
    }
}
