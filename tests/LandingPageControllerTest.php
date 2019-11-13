<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class LandingPageControllerTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    public function testHomeage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /* Test jumbotron */
        $this->assertSelectorTextContains('.jumbotron h1', 'Welcome!');
        $this->assertSelectorTextContains('.jumbotron blockquote', 'If you have knowledge, let others light their candles in it.');

        /* Test 'apply' link */
        $linkApply = $crawler
            ->filter('.jumbotron p.lead a:contains("Apply")')
            ->eq(0) // select the first link in the list // ->first()
            ->link()
        ;
    }

    public function testHomepageAbout(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/about');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // $crawler = $client->click($linkApply);
        // $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /* Test about list */
        $this->assertEquals('About', $crawler->filter('#about h2')->text());
        // traversing
        $this->assertCount(11, $crawler->filter('#about .list-group .list-group-item'));
    }

    /**
     * @dataProvider provideUrls
     * successful, has nav, footer
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $crawler = $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());

        /* Test main nav */
        // $homepageLink = $crawler
        //     ->filter('nav a:contains("Mentoring Programme")')
        //     ->eq(0)
        //     ->link();

        $this->assertCount(5, $crawler->filter('nav ul li a'));

        /* Test footer */
        $this->assertSelectorTextContains('footer a', 'Natalia Stanko');
    }

    public function provideUrls()
    {
        return [
            ['/'],
            ['/about'],
        ];
    }
}
