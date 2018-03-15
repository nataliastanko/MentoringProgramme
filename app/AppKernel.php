<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new A2lix\TranslationFormBundle\A2lixTranslationFormBundle(),
            new Knp\DoctrineBehaviors\Bundle\DoctrineBehaviorsBundle(),
            new ADesigns\CalendarBundle\ADesignsCalendarBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new SC\DatetimepickerBundle\SCDatetimepickerBundle(),
            new Presta\SitemapBundle\PrestaSitemapBundle(),
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            new Isometriks\Bundle\SpamBundle\IsometriksSpamBundle(),
            new Petkopara\MultiSearchBundle\PetkoparaMultiSearchBundle(),
            new Liuggio\ExcelBundle\LiuggioExcelBundle(),
            // new cspoo\Swiftmailer\MailgunBundle\cspooSwiftmailerMailgunBundle(),

            new Wit\Program\Admin\EditionBundle\WitProgramAdminEditionBundle(),
            new Wit\Program\Admin\GalleryBundle\WitProgramAdminGalleryBundle(),
            new Wit\Program\Admin\UserBundle\WitProgramAdminUserBundle(),
            new Wit\Program\Admin\QuestionnaireBundle\WitProgramAdminQuestionnaireBundle(),
            new Wit\Program\Admin\SiteContentBundle\WitProgramAdminSiteContentBundle(),
            new Wit\Program\Account\UserBundle\WitProgramAccountUserBundle(),
            new Wit\Program\CalendarBundle\WitProgramCalendarBundle(),
            new Wit\SiteBundle\WitSiteBundle(),
            new Wit\Program\Account\MentorBundle\WitProgramAccountMentorBundle(),
            new Wit\Program\Account\MenteeBundle\WitProgramAccountMenteeBundle(),
            new Wit\Program\Admin\CalendarBundle\WitProgramAdminCalendarBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getProjectDir().'/app/config/'.$this->getEnvironment().'/config.yml');
        // $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
