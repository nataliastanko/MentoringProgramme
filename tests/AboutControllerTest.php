<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
// use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
// use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;

class AboutControllerTest extends WebTestCase
{
    // populates the database only one time, then wraps every tests in a transaction that will be rolled back at the end after its execution
    // use RefreshDatabaseTrait;

    // if you're using in memory SQLite for your tests, use RecreateDatabaseTrait to create the database schema "on the fly":

    use RecreateDatabaseTrait;

    public function testListView()
    {
        $client = static::createClient();
        $crawlerIndex = $client->request('GET', '/admin/about/');

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

        $faker = \Faker\Factory::create();
        $titleExpected = $faker->text(255);

        $formCreate = $crawlerNew->selectButton('Save')->form();
        $formCreate->setValues([
            'about[title]' => $titleExpected,
            'about[content]' => $faker->text(),
        ]);
        $crawlerNew = $client->submit($formCreate);

        $crawlerIndex = $client->request('GET', '/admin/about/');
        $this->assertCount(12, $crawlerIndex->filter('table tbody tr'));

        /* Test 'edit' link */
        $linkEdit = $crawlerIndex->filter('table tbody tr td a:contains("edit")')
            ->last() // sorting dependent @TODO sort by position
            ->link()
        ;
        $crawlerEdit = $client->click($linkEdit);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $formUpdate = $crawlerEdit->selectButton('Update')->form();
        $this->assertEquals(
            $formUpdate->get('about[title]')->getValue(),
            $titleExpected
        );

        $contentExpected = $faker->text();
        $formUpdate['about[content]'] = $contentExpected;
        $crawlerEdit = $client->submit($formUpdate);

        $crawlerEdit = $client->click($linkEdit);
        $formUpdate = $crawlerEdit->selectButton('Update')->form();
        $this->assertEquals(
            $formUpdate->get('about[content]')->getValue(),
            $contentExpected
        );

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
        $this->assertStringContainsString(
            $contentExpected,
            $contentTh->siblings()->filter('td')->text()
        );
    }

    public function testListViewNoRecords()
    {
        /* Test with purged database */
        // $kernel = self::bootKernel();
        // $em = $kernel->getContainer()
        //     ->get('doctrine.orm.default_entity_manager');
        // $purger = new ORMPurger();
        // $executor = new ORMExecutor($em, $purger);
        // // $executor->execute($loader->getFixtures());

        // $crawlerIndex = $client->request('GET', '/admin/about/');

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
        $crawlerIndex = $client->request('GET', '/admin/about/');
        $this->assertCount(11, $crawlerIndex->filter('table tbody tr'));
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
            $client->getResponse()->isRedirect('/admin/about/')
        );
        // $this->assertTrue($client->getResponse()->isRedirect());

        $crawlerIndex = $client->followRedirect();
        $this->assertCount(10, $crawlerIndex->filter('table tbody tr'));
    }

    public function testCreationEmoji()
    {
        $client = static::createClient();
        $crawlerNew = $client->request('GET', '/admin/about/new');

        $faker = \Faker\Factory::create();
        $form = $crawlerNew->selectButton('Save')->form();
        $form['about[title]'] = '😱';
        $form['about[content]'] = $faker->text();
        $client->submit($form);

        $crawlerIndex = $client->request('GET', '/admin/about/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(12, $crawlerIndex->filter('table tbody tr'));
    }

    public function testCreationEmptyFields()
    {
        $client = static::createClient();
        $crawlerNew = $client->request('GET', '/admin/about/new');

        $form = $crawlerNew->selectButton('Save')->form();
        $form->setValues([
            'about[title]' => '',
            'about[content]' => '',
        ]);
        $crawlerNew = $client->submit($form);

        $this->assertStringContainsString(
            'This value should not be blank.',
            $crawlerNew->filter('label[for="about_title"] .form-error-message')->text()
        );
        $this->assertStringContainsString(
            'This value should not be blank.',
            $crawlerNew->filter('label[for="about_content"] .form-error-message')->text()
        );

        $crawlerIndex = $client->request('GET', '/admin/about/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // number of items is not changed
        $this->assertCount(11, $crawlerIndex->filter('table tbody tr'));
    }

    public function testCreationTextTooLong()
    {
        $client = static::createClient();
        $crawlerNew = $client->request('GET', '/admin/about/new');

        $faker = \Faker\Factory::create();

        $form = $crawlerNew->selectButton('Save')->form();
        $form['about[title]'] = $faker->lexify(str_repeat('?', 256));

        $crawlerNew = $client->submit($form);

        $this->assertStringContainsString(
            'Cannot be longer than 255 characters.',
            $crawlerNew->filter('label[for="about_title"] .form-error-message')->text()
        );

        $crawlerIndex = $client->request('GET', '/admin/about/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // number of items is not changed
        $this->assertCount(11, $crawlerIndex->filter('table tbody tr'));
    }
}
