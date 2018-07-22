<?php

namespace SiteBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
// use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler\Crawler;

/**
 *
 * @author  Natalia Stanko <contact@nataliastanko.com>
 * @license MIT License (MIT)
 *
 * Test main page
 */
class HomepageControllerTest extends AbstractControllerTest
{
    private $crawler;

    public function setUp()
    {
        parent::setUp();

        /**
         * @var $this->crawler Crawler
        */
        $this->crawler = $this->client->request('GET', '/'); // route name 'homepage_locale'
    }

    /**
     * @covers HomepageController::index
     * @group homepage
     * @group edition
     * @test
     */
    public function loadMainPage()
    {
        /* test status code */
        $this->assertEquals(
            Response::HTTP_OK, //200
            $this->client->getResponse()->getStatusCode()
        );

        /* test page title */
        $this->assertContains(
            $this->translator->trans('mentoring.program'),
            $this->crawler
                ->filter('title')
                ->text()
        );
    }

    /**
     * @covers HomepageController::index
     * @group homepage
     * @group edition
     * @group buttons
     * @group menu
     * @group multilanguage
     * @test
     */
    public function mainPageMenu()
    {
        /* test main menu */
        $dropdown = $this->crawler->filter('.container ul.nav.navbar-nav li.dropdown ul');
        $menu = $this->crawler->filter('.container ul.nav.navbar-nav>li');

        $menuIems = $menu->reduce(
            function ($node, $i) {
                if ($node->filter('img')->count()) {
                    return false;
                }
                else if (!$node->attr('class')) {
                    return true;
                }
                else if ($node->attr('class') == 'dropdown') {
                    return true;
                } else {
                    return false;
                }
            }
        );

        $this->assertEquals(
            7,
            $menuIems->count()
        );

        /* test lang choice in main menu */
        $langIems = $menu->reduce(
            function ($node, $i) {
                if ($node->filter('img')->count()) {
                    return false;
                }
                else if (!$node->attr('class')) {
                    return false;
                }
                else if ($node->attr('class') == 'lang') {
                    return true;
                } else {
                    return false;
                }
            }
        );

        $this->assertEquals(
            2,
            $langIems->count()
        );

        // check lang buttons names
        $this->assertContains(
            'EN',
            $langIems->eq(0)->text()
        );
        $this->assertContains(
            'PL',
            $langIems->eq(1)->text()
        );

        // check lang buttons urls

        /**
         * @TODO check urls
         */

        // $this->assertEquals(
        //     $langIems->eq(0)->attr('href'),
        //     $this->router->generate(
        //         'edition_show',
        //         ['_locale' => "en",
        //         'edition'=> 2 // @FIXME last edition if exist
        //         ],
        //         false
        //     ) // '/en/edition/2'
        // );

        $this->markTestIncomplete(
            'Sprawdź poprawność linków do zmiany języka strony'
        );
    }

    /**
     * @covers HomepageController::index
     * @group homepage
     * @group apply
     * @group mentee
     * @group mentor
     * @group partner
     * @group buttons
     * @test
     */
    public function applyButtons()
    {
        /* test buttons */
        $mainButtons = $this->crawler->filter('.jumbotron .main-button a');

        // count buttons
        $this->assertEquals(
            3,
            $mainButtons->count()
        );

        // check buttons names
        $this->assertContains(
            $this->translator->trans('button.be_mentee'),
            $mainButtons->eq(0)->text()
        );
        $this->assertContains(
            $this->translator->trans('button.be_mentor'),
            $mainButtons->eq(1)->text()
        );
        $this->assertContains(
            $this->translator->trans('button.be_partner'),
            $mainButtons->eq(2)->text()
        );

        // check buttons urls

        $this->assertEquals(
            $mainButtons->eq(0)->attr('href'),
            $this->router->generate('mentee_apply', [], false) // '/pl/mentee/apply'
        );
        $this->assertEquals(
            $mainButtons->eq(1)->attr('href'),
            $this->router->generate('mentor_apply', [], false) // '/pl/mentee/apply'
        );
        $this->assertEquals(
            $mainButtons->eq(2)->attr('href'),
            'mailto:'.urlencode(
                $emailPartnerParam = static::$kernel->getContainer()
                    ->getParameter('email_partner')
            )
        );
    }

    /**
     * @TODO
     * @covers HomepageController::index
     * @group homepage
     * @group edition
     * @group mentor
     * @test
     */
    public function mentorsOnMainPage()
    {
        $this->markTestIncomplete(
            'Mentorzy dla różnych edycji, jeśli istnieją, jeśli jest jakaś edycja'
        );
    }

    /**
     * @TODO in every edition if editions exist
     * @covers HomepageController::index
     * @group homepage
     * @group edition
     * @group mentor
     * @test
     */
    public function partnersOnMainPage()
    {
        $this->markTestIncomplete(
            'Partnerzy dla różnych edycji, jeśli istnieją, jeśli jest jakaś edycja'
        );
    }

    /**
     * @TODO
     * @covers HomepageController::index
     * @group homepage
     * @group edition
     * @group gallery
     * @test
     */
    public function isGalleryInitialized()
    {
        $this->markTestIncomplete(
            'Czy galeria się ładuje, jeśli istnieje'
        );
    }

    /**
     * @TODO
     * @covers HomepageController::index
     * @group homepage
     * @group contact
     * @test
     */
    public function contactInfo()
    {
        $this->markTestIncomplete(
            'Sprawdź poprawność danych kontaktowych'
        );
    }

    /**
     * @TODO
     * @covers HomepageController::index
     * @group homepage
     * @group multilanguage
     * @test
     */
    public function multilanguage()
    {
        /**
         * test multilanguage
         */

        /**
         * @var $crawler Crawler
        */
        $crawler = $this->client->request('GET', '/en'); // route name 'homepage'
        // test code
        $this->assertEquals(301, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $this->client->followRedirect();
        /**
         * @var $crawler Crawler
        */
        $crawler = $this->client->getCrawler();

        $this->assertContains(
            '/en/',
            $this->client->getRequest()->getUri()
        );

        /**
         * @var $crawler Crawler
        */
        $crawler = $this->client->request('GET', '/pl'); // route name 'homepage'
        // test code
        $this->assertEquals(301, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $this->client->followRedirect();
        /**
         * @var crawler Crawler
        */
        $crawler = $this->client->getCrawler();

        // Stop here and mark this test as incomplete.
        // $this->markTestIncomplete(
        //   'TODO test multilanguage'
        // );
    }

    /**
     * @TODO
     * @covers HomepageController::index
     * @group homepage
     * @group multilanguage
     * @test
     */
    public function homepageWhenLoggedIn()
    {
        $this->markTestIncomplete(
            'Jak wygląda strona główna, jeśli użytkownik jest zalogowany'
        );
    }
}
