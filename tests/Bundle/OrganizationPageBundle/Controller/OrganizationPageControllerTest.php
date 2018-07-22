<?php

namespace OrganizationPageBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class OrganizationPageControllerTest extends WebTestCase
{

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
    public function testMainPage(): void
    {
        $client = $this->makeClient();

        // subdomain
        $domain = 'poland' . '.' . $client->getContainer()->getParameter('domain_name') . '.' . $client->getContainer()->getParameter('domain_ext');

        $client->setServerParameter('HTTP_HOST', $domain);
        $crawler = $client->request('GET', '/');
        $this->isSuccessful($client->getResponse());
        $this->assertStatusCode(200, $client);

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
            strtoupper('poland'),
            $crawler->filter('.bglead > h2')->text()
        );

        // en
        // check subhead .bglead h3 text
        $this->assertSame(
            'Mentoring program for women',
            $crawler->filter('.bglead > h3')->text()
        );

    }
}
