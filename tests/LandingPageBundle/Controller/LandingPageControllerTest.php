<?php

namespace LandingPageBundle\Tests\Controller;

// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * PHPUnit is configured by the phpunit.xml.dist file in the root of your Symfony application.
 *
 * == Functional Tests ==
 * @link https://symfony.com/doc/3.4/testing.html
 * Define a functional test that at least checks if your application pages are successfully loading.
 * Hardcode the URLs used in the functional tests instead of using the URL generator.
 * ====
 * Make a request;
 * Click on a link or submit a form;
 * Test the response;
 * Rinse and repeat.
 * ====
 *
 * LiipFunctionalTestBundle provides additional functional test-cases for Symfony applications
 * @link https://github.com/liip/LiipFunctionalTestBundle/tree/2.x
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

        $files = [
                __DIR__ . '/../../../app/Resources/fixtures/must_organization.yml',
                __DIR__ . '/../../../app/Resources/fixtures/must_config.yml',
                __DIR__ . '/../../../app/Resources/fixtures/must_section_config.yml',
                __DIR__ . '/../../../app/Resources/fixtures/must_edition.yml',
            ];

        // $append = true; It allows you only to remove all records of table. Values of auto increment won't be reset.
        $fixtures = $this->loadFixtureFiles($files, true);

        // The second way is consisted in using the second parameter $append with value true and the last parameter $purgeMode with value Doctrine\Common\DataFixtures\Purger\ORMPurger::PURGE_MODE_TRUNCATE. It allows you to remove all records of tables with resetting value of auto increment.
        // $fixtures = $this->loadFixtureFiles($files, true, null, 'doctrine', \Doctrine\Common\DataFixtures\Purger\ORMPurger::PURGE_MODE_TRUNCATE );

    }

    /**
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

        // landingpgae
        $domain = $client->getContainer()->getParameter('domain_name') . '.' . $client->getContainer()->getParameter('domain_ext');

        $client->setServerParameter('HTTP_HOST', $domain);

        $crawler = $client->request('GET', '/');
        $this->isSuccessful($client->getResponse());
        $this->assertStatusCode(200, $client);

        // There is one <body> tag
        $this->assertSame(
            1,
            $crawler->filter('html > body')->count()
        );

        // There is one <h1> in .bglead
        $this->assertSame(
            1,
            $crawler->filter('.bglead  > h1')->count()
        );

        // There is one <h1> in .bglead
        $this->assertContains(
            'Tech Leaders',
            $crawler->filter('.bglead > h1')->text()
        );
        // There is one <h1> in .bglead
        $this->assertContains(
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
    }
}
