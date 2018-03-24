<?php

namespace AdminBundle\Service\Locale;

use A2lix\TranslationFormBundle\Locale\DefaultProvider;
use A2lix\TranslationFormBundle\Locale\LocaleProviderInterface;
use SiteBundle\Service\SubdomainDetection;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class LocaleFormProvider extends DefaultProvider implements LocaleProviderInterface
{
    /** @var SubdomainDetection */
    private $subdomainDetection;

    /** @var \Entity\Organization */
    private $organization;

    /**
     * @param array $locales
     * @param       $defaultLocale
     * @param array $requiredLocales
     */
    public function __construct(array $locales, $defaultLocale, array $requiredLocales = array(), SubdomainDetection $subdomainDetection)
    {
        parent::__construct($locales, $defaultLocale, $requiredLocales);
        $this->subdomainDetection = $subdomainDetection;

        $this->organization = $this->subdomainDetection->getOrganization();
    }

    /**
     * {@inheritdoc}
     * array(2) { [0]=> string(2) "en" [1]=> string(2) "pl" }
     */
    public function getLocales()
    {
        return $this->organization->getLocales();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale()
    {
        return $this->organization->getDefaultLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredLocales()
    {
        return $this->organization->getRequiredLocales();
    }

}
