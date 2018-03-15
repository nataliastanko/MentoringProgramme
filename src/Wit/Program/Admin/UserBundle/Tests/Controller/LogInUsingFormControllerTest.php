<?php

namespace Wit\Program\Admin\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler\Form;

/**
 * @author  Natalia Stanko <contact@nataliastanko.com>
 * @license MIT License (MIT)
 *
 * Log in using form
 */
class LogInUsingFormControllerTest extends LogInControllerTest
{
    /**
     * @TODO
     * @security
     * @user
     * @form
     * @test
     */
    public function loginWithFormManually()
    {
        /**
         * @var $crawler Crawler
        */
        $crawler = $this->client->request('GET', '/login');

        // select button tags
        // selectButton() uses several parts of the buttons to find them:
        // The value attribute value
        // The id or alt attribute value for images;
        // The id or name attribute value for button tags.
        /**
         * @var $buttonCrawlerNode Crawler
        */
        $buttonCrawlerNode = $crawler->selectButton('_submit');

        /**
         * @var $form Form
        */
        $form = $buttonCrawlerNode->form(
            array(
            '_username' => 'abc@def.ghj',
            '_password' => 'klmn',
            )
        );

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();

        // actual route
        $actualRoute = $this->client->getRequest()->getUri();

        // check page redirected to
        /**
         * @var $redirectedPageToCrawler Crawler
        */
        $redirectedPageToCrawler = $this->client->getCrawler();

        $this->assertEquals(
            Response::HTTP_OK, // 200
            $this->client->getResponse()->getStatusCode()
        );

        // route actual same as expected
        $this->assertRegExp(
            '/\/account\/$/',
            $actualRoute
        );

        // $form = $crawler->selectButton('_submit')->form(array(
        //     '_username'  => $username,
        //     '_password'  => $password,
        //     ));
        // $this->client->submit($form);
    }

}
