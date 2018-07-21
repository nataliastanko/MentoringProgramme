<?php

namespace LandingPageBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    /**
     * Smoke testing
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public function testLandingPage()
    {
        $client = self::createClient();

        $domain = $client->getContainer()->getParameter('domain_name') . '.' . $client->getContainer()->getParameter('domain_ext');

        $client->setServerParameter('HTTP_HOST', $domain);

        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isSuccessful());

     }

    /**
     * Smoke testing
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();

        $domain = $client->getContainer()->getParameter('domain_name') . '.' . $client->getContainer()->getParameter('domain_ext');

        $client->setServerParameter('HTTP_HOST', $domain);
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        $client = self::createClient();

        return array(
            // [$client->getContainer()->get('router.default')->generate('landingpage')],
            array('/'),
            // array('/blog'),
            // // ...
        );
    }

}
