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

            if (!$object->getPartnersEmail()) {
                $context->buildViolation('config.partners.email.notBlank')
                    ->atPath('isSignupPartnersEnabled')
                    ->addViolation();
                $context->buildViolation('config.partners.email.notBlank')
                    ->atPath('partnersEmail')
                    ->addViolation();
            }
        }
    }

    /**
     * @param  Config $object
     * @param  ExecutionContextInterface $context
     * @param  $payload
     * @return void
     */
    public static function checkMenteesExternalSignupUrl(Config $object, ExecutionContextInterface $context, $payload)
    {
        $organization = $object->getOrganization();
        $buttonsSectionsEnabled = $organization->getButtonsSectionsEnabledArray();

        // if menteesExternalSignup enabled
        if (isset($buttonsSectionsEnabled['menteesExternalSignup']) && $object->getIsSignupMenteesEnabled()) {

            if (!$object->getMenteesExternalSignupUrl()) {
                $context->buildViolation('config.mentees.externalSignupUrl.notBlank')
                    ->atPath('isSignupMenteesEnabled')
                    ->addViolation();
                $context->buildViolation('config.mentees.externalSignupUrl.notBlank')
                    ->atPath('menteesExternalSignupUrl')
                    ->addViolation();
            }
        }
    }
}
