<?php

namespace Tests\OrganizationPage;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
abstract class AbstractOrganizationPageTest extends WebTestCase
{
    /**
     * @var $client Client
     */
    protected $client;

    /**
     * @var $translator Translator
     */
    protected $translator;

    /**
     * @var $router Router
     */
    protected $router;

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        self::bootKernel();
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before every test method is executed.
     * tearDown is called after every test method is executed.
     */
    public function setUp()
    {
        parent::setUp();

        self::bootKernel();

        // returns the real and unchanged service container
        $container = self::$kernel->getContainer();

        // gets the special container that allows fetching private services
        // $container = self::$container; // from Symfony 4.1

        $kernalDir = $container->get('kernel')->getRootDir();

        // Load Fixtures Using Alice
        $files = [
            $kernalDir . '/Resources/fixtures/must_organization.yml',
            $kernalDir . '/Resources/fixtures/must_config.yml',
            $kernalDir . '/Resources/fixtures/must_section_config.yml',
            $kernalDir . '/Resources/fixtures/must_edition.yml',
        ];

        // $append = true; It allows you only to remove all records of table. Values of auto increment won't be reset.
        $fixtures = $this->loadFixtureFiles($files, true);

        /**
         * @todo make it work
         */
        // The second way is consisted in using the second parameter $append with value true and the last parameter $purgeMode with value Doctrine\Common\DataFixtures\Purger\ORMPurger::PURGE_MODE_TRUNCATE. It allows you to remove all records of tables with resetting value of auto increment.
        // $fixtures = $this->loadFixtureFiles($files, true, null, 'doctrine', \Doctrine\Common\DataFixtures\Purger\ORMPurger::PURGE_MODE_TRUNCATE );

        /**
         * @var $this->client Client default client set
        */
        // $this->client = static::createClient();

        // As mentioned in Symfony documentation, you should use absolute urls, because change of URLs will impact your end users and thats what you wanna cover in your functional tests too.
        // "Hardcoding the request URLs is a best practice for functional tests. If the test generates URLs using the Symfony router, it won't detect any change made to the application URLs which may impact the end users."
        // but router is helpful
        // Symfony simulates an http client and tests against an instance of the Kernel created for that test. There are no web servers involved.

        /**
         * @var $this->router Router
        */
        // $this->router = $this->getClient()->getContainer()->get('router');

        // $this->translator = static::$kernel->getContainer()
            // ->get('translator.default');
        /**
         * @var $this->em Doctrine\ORM\EntityManager
        */
         // $this->em = static::$kernel->getContainer()
         //     ->get('doctrine.orm.entity_manager');
    }

    // /**
    //  * Log in user
    //  *
    //  * @return Client when user authorized
    //  */
    // protected function createAuthorizedClient()
    // {
    //     $this->client = static::createClient();

    //     return $client;
    // }

    // public function getClient()
    // {
    //     return $this->client;
    // }
}
