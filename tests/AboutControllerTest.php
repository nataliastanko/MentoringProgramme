<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
// use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
// use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class AboutControllerTest extends WebTestCase
{
    // populates the database only one time, then wraps every tests in a transaction that will be rolled back at the end after its execution
    use RefreshDatabaseTrait;

    public function testListView()
    {
        $client = static::createClient();
        $crawlerIndex = $client->request('GET', '/about/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'About index');

        /* Test list */
        $this->assertCount(11, $crawlerIndex->filter('table tbody tr'));

        /* Test 'create new' link */
        $linkCreate = $crawlerIndex
            ->filter('a:contains("Create new")')
            ->eq(0) // select the first link in the list
            ->link()
        ;
        $crawlerNew = $client->click($linkCreate);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $formCreate = $crawlerNew->selectButton('Save')->form();
        $formCreate['about[title]'] = 'About it';
        $formCreate['about[content]'] = '';
        $crawlerNew = $client->submit($formCreate);

        $crawlerIndex = $client->request('GET', '/about/');
        $this->assertCount(12, $crawlerIndex->filter('table tbody tr'));

        /* Test 'edit' link */
        $linkEdit = $crawlerIndex->filter('table tbody tr td a:contains("edit")')
            ->last() // sorting dependent @TODO sort by position
            ->link()
        ;
        $crawlerEdit = $client->click($linkEdit);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $formUpdate = $crawlerEdit->selectButton('Update')->form();
        $title = $formUpdate->get('about[title]')->getValue();
        $this->assertEquals($title, 'About it');
        $formUpdate['about[content]'] = 'Pusheen the cat';
        $crawlerEdit = $client->submit($formUpdate);

        $crawlerEdit = $client->click($linkEdit);
        $formUpdate = $crawlerEdit->selectButton('Update')->form();
        $content = $formUpdate->get('about[content]')->getValue();
        $this->assertEquals($content, 'Pusheen the cat');

        /* Test 'show' link */
        $linkShow = $crawlerIndex->filter('table tbody tr td a:contains("show")')
            ->last()
            ->link()
        ;
        $crawlerShow = $client->click($linkShow);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $contentTh = $crawlerShow
            ->filter('table tbody th')
            ->reduce(function (Crawler $node) {
                return $node->text() === 'Content';
            });
        $this->assertStringContainsString('Pusheen the cat', $contentTh->siblings()->filter('td')->text());

        /* Test with purged database */
        // $kernel = self::bootKernel();
        // $em = $kernel->getContainer()
        //     ->get('doctrine.orm.default_entity_manager');
        // $purger = new ORMPurger();
        // $executor = new ORMExecutor($em, $purger);
        // // $executor->execute($loader->getFixtures());

        // $crawlerIndex = $client->request('GET', '/about/');

        // $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // $this->assertCount(1, $crawlerIndex->filter('table tbody tr'));
        // $this->assertEquals(
        //     'no records found',
        //     $crawlerIndex->filter('table tbody tr td')->first()->text()
        // );
        $this->markTestIncomplete();
    }

    public function testShowSingleView()
    {
        $client = static::createClient();
        $crawlerIndex = $client->request('GET', '/about/');
        $this->assertCount(12, $crawlerIndex->filter('table tbody tr'));
        $linkShow = $crawlerIndex->filter('table tbody tr td a:contains("show")')
            ->first()
            ->link()
        ;
        $crawlerShow = $client->click($linkShow);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /* Test deletion */
        $form = $crawlerShow->selectButton('Delete')->form();
        $client->submit($form);
        $this->assertTrue(
            $client->getResponse()->isRedirect('/about/')
        );
        // $this->assertTrue($client->getResponse()->isRedirect());

        $crawlerIndex = $client->followRedirect();
        $this->assertCount(11, $crawlerIndex->filter('table tbody tr'));
    }

    public function testCreation()
    {
        $client = static::createClient();
        $crawlerNew = $client->request('GET', '/about/new');

        $form = $crawlerNew->selectButton('Save')->form();
        $form['about[title]'] = '😱';
        $form['about[content]'] = '';
        $client->submit($form);

        $crawlerIndex = $client->request('GET', '/about/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(12, $crawlerIndex->filter('table tbody tr'));

        $linkCreate = $crawlerIndex
            ->filter('a:contains("Create new")')
            ->eq(0) // select the first link in the list
            ->link()
        ;
        $crawlerNew = $client->click($linkCreate);

        $form = $crawlerNew->selectButton('Save')->form();
        $form['about[title]'] = '';
        $form['about[content]'] = '';
        $client->submit($form);

        // too long text, short text, no text, validation messages etc
        $this->markTestIncomplete();
    }
}
