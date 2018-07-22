<?php

namespace Tests\LandingPage;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Landingpage smoke tests
 */
class DefaultControllerTest extends WebTestCase
{
//     /**
//      * @param  string $url url to test
//      * @return void
//      */
//     public function testLandingPage(): void
//     {
//         $client = self::createClient();

//         $domain = $client->getContainer()->getParameter('domain_name') . '.' . $client->getContainer()->getParameter('domain_ext');

//         $client->setServerParameter('HTTP_HOST', $domain);

//         $crawler = $client->request('GET', '/');
//         $this->assertTrue($client->getResponse()->isSuccessful());
//      }

    /**
     * @param  string $url url to test
     * @dataProvider urlProvider
     * @param  string $url url to test
     */
    public function testPageIsSuccessful($url): void
    {
        $client = self::createClient();

        $domain = $client->getContainer()->getParameter('domain_name') . '.' . $client->getContainer()->getParameter('domain_ext');

        $client->setServerParameter('HTTP_HOST', $domain);
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * Urls to check
     * @return array of urls
     */
    public function urlProvider(): array
    {
        $client = self::createClient();

        return [
            // [$client->getContainer()->get('router.default')->generate('landingpage')],
            ['/'],
            // array('/blog'),
            // // ...
        ];
    }

}
