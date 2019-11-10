<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AboutControllerTest extends WebTestCase
{
    public function testAboutIndexList()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/about/');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('h1', 'About index');
    }
}
