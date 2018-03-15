<?php

namespace Wit\SiteBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * @link https://symfony.com/doc/current/testing.html#functional-tests
 *
 * Functional Tests have a very specific workflow:
 *
 * Make a request;
 * Test the response;
 * Click on a link or submit a form;
 * Test the response;
 * Rinse and repeat.
 *
 * @author  Natalia Stanko <contact@nataliastanko.com>
 * @license MIT License (MIT)
 *
 * UnitTests Annotations doc
 * @link https://phpunit.de/manual/3.7/en/appendixes.annotations.html#appendixes.annotations.group
 */
abstract class AbstractControllerTest extends WebTestCase
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
        /**
         * @var $this->client Client default client set
        */
        $this->client = static::createClient();

        // As mentioned in Symfony documentation, you should use absolute urls, because change of URLs will impact your end users and thats what you wanna cover in your functional tests too.
        // "Hardcoding the request URLs is a best practice for functional tests. If the test generates URLs using the Symfony router, it won't detect any change made to the application URLs which may impact the end users."
        // but router is helpful
        // Symfony simulates an http client and tests against an instance of the Kernel created for that test. There are no web servers involved.

        /**
         * @var $this->router Router
        */
        $this->router = $this->getClient()->getContainer()->get('router');

        $this->translator = static::$kernel->getContainer()
            ->get('translator.default');
        /**
         * @var $this->em Doctrine\ORM\EntityManager
        */
         $this->em = static::$kernel->getContainer()
             ->get('doctrine.orm.entity_manager');
    }

    /**
     * Log in user
     *
     * @return Client when user authorized
     */
    protected function createAuthorizedClient()
    {
        $this->client = static::createClient();

        return $client;
    }

    public function getClient()
    {
        return $this->client;
    }

}
