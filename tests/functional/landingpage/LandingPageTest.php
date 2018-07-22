<?php

namespace Tests\LandingPage;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * PHPUnit is configured by the phpunit.xml.dist file in the root of your Symfony application.
 *
 * == Functional Tests ==
 * @link https://symfony.com/doc/3.4/testing.html
 * test a "suite" of functionalities - interactions, db interactions, web services
 * Define a functional test that at least checks if your application pages are successfully loading.
 * Hardcode the URLs used in the functional tests instead of using the URL generator.
 *
 * Functional Tests have a very specific workflow:
 * Make a request;
 * Click on a link or submit a form;
 * Test the response;
 * Rinse and repeat.
 *
 * LiipFunctionalTestBundle provides additional functional test-cases for Symfony applications
 * @link https://github.com/liip/LiipFunctionalTestBundle/tree/2.x
 *
 * == PhpInit Tests assertions ==
 * @link https://phpunit.readthedocs.io/en/7.1/assertions.html
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class LandingPageControllerTest extends WebTestCase
{
    // protected static function getKernelClass(): string
    // {
    //     return \AppKernel::class;
    // }

    private $router; // consider that this holds the Symfony router service

    /**
     * Load Fixtures Using Alice
     * {@inheritdoc}
     */
     public function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        // returns the real and unchanged service container
        $container = self::$kernel->getContainer();

        // gets the special container that allows fetching private services
        // $container = self::$container; // from Symfony 4.1

        $kernalDir = $container->get('kernel')->getRootDir();

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

    }

    /**
     * Landingpage test:
     * en lang only displayed
     *
     * request(
        $method,
        $uri,
        array $parameters = array(),
        array $files = array(),
        array $server = array(),
        $content = null,
        $changeHistory = true
        )
     *
     * @return void
     */
    public function testOrganizationList(): void
    {
        $client = $this->makeClient();

        $domain = $client->getContainer()->getParameter('domain_name') . '.' . $client->getContainer()->getParameter('domain_ext');

        // set it only once
        $client->setServerParameter('HTTP_HOST', $domain);

        // enables the profiler for the very next request
        $client->enableProfiler();

        // request
        $crawler = $client->request('GET', '/');

        // status code
        $this->isSuccessful($client->getResponse()); // Successful HTTP request
        $this->assertStatusCode(200, $client); // Standard response for successful HTTP request

        // There is one <h1> in .bglead
        $this->assertSame(
            1,
            $crawler->filter('.bglead > h1')->count()
        );

        // check head .bglead h1 text
        $this->assertSame(
            'Tech Leaders',
            $crawler->filter('.bglead > h1')->text()
        );

        // check subhead .bglead h2 text
        $this->assertSame(
            'Mentoring program for women',
            $crawler->filter('.bglead > h2')->text()
        );

        $content = $client->getResponse()->getContent();
        // `filter()` can't be used since the output is HTML code,
        // check the content directly
        // $this->assertContains(
        //     '<h2>Invest in yourself.</h2>',
        //     $content
        // );

        // subdomain
        $domain = 'poland' . '.' . $client->getContainer()->getParameter('domain_name') . '.' . $client->getContainer()->getParameter('domain_ext');

        $client->setServerParameter('HTTP_HOST', $domain);
        $crawler = $client->request('GET', '/');
        $this->isSuccessful($client->getResponse());
        $this->assertStatusCode(200, $client);

        // access the Profiler Data
        // check that the profiler is enabled
        if ($profile = $client->getProfile()) {
            // check the number of requests
            $this->assertEquals(
                2, // images and organizations
                $profile->getCollector('db')->getQueryCount()
            );

            // too many DB queries for instance
            // $this->assertLessThan(
            //     20,
            //     $profile->getCollector('db')->getQueryCount(),
            //     sprintf(
            //         'Checks that query count is less than 30 (token %s)',
            //         $profile->getToken() // Web Profiler token
            //     )
            // );

        }
    }
}
