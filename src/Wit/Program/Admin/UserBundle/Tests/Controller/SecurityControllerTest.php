<?php

namespace Wit\Program\Admin\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author  Natalia Stanko <contact@nataliastanko.com>
 * @license MIT License (MIT)
 *
 * Test security
 * when not logged in
 *
 * @TODO
 */
class SecurityControllerTest extends LogInControllerTest
{
    // public function setUp()
    // {
    //     parent::setUp();
    // }

    /**
     * Data providers are pretty much the best thing to happen to automated testing.
     * Instead of testing a single scenario,
     * you can instead test a whole range of permutations in order to find those bugs.
     * You start by declaring an annotation @dataProvider for your test method
     * Must be public method.
     *
     * Data provider for test notLoggedInProtectedUrls().
     *
     * The items in the data provider method are passed one at a time to the testing method,
     * in the same order they are declared.
     *
     * @return an array of test case, array of values (urls) to check
     *   Nested arrays of values to check:
     *   - $expectedUrl
     *   - $urlName
     */
    public function protectedUrlsDataProvider()
    {
        return [
            [
                '/account/',
                'account'
            ],
            [
                '/account/profile/',
                'fos_user_profile_show'
            ],
            [
                '/admin/user/',
                'user_index'
            ]
        ];
    }

    /**
     * Check if not logged in people can access routes for logged in people
     *
     * @covers       urls security config
     * @dataProvider protectedUrlsDataProvider
     * @security
     * @user
     * @test
     */
    public function notLoggedInProtectedUrls($expectedUrl, $urlName)
    {
        $url = $this->router->generate($urlName, [], false);

        // check if routes are as expected
        $this->assertEquals(
            $expectedUrl,
            $url
        );

        /**
         * @var $accountCrawler Crawler
        */
        $this->client->request('GET', $url);

        /* test status code */
        $this->assertEquals(
            Response::HTTP_FOUND, // 302
            $this->client->getResponse()->getStatusCode()
        );

        /* check redirect */
        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);
        $this->assertTrue($this->client->getResponse()->isRedirection());

        // target route
        $targetRoute = $this->client->getResponse()->getTargetUrl(); // absolute address with domain
        // the same value
        // $targetRoute = $this->client->getResponse()->headers->get('location'); // absolute address with domain

        // do redirect
        $this->client->followRedirect();
        // $this->client->followRedirects(); // automatically follow all redirects

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

        // route expected same as target
        $this->assertEquals(
            $targetRoute,
            $actualRoute
        );

        // route actual same as expected
        $this->assertRegExp(
            '/\/login$/',
            $actualRoute
        );

        $this->loginPageContent($redirectedPageToCrawler);
    }

    /**
     * Data provider for test notLoggedInAllowedUrls().
     *
     * @return an array of test case, array of values (urls) to check
     *   Nested arrays of values to check:
     *   - $expectedUrl
     *   - $urlName
     *   - $parameters
     */
    public function allowedUrlsDataProvider()
    {
        return [
            [
                '/',
                'homepage_locale'
            ],
            [
                '/resetting/request',
                'fos_user_resetting_request'
            ],
            [
                '/sitemap.xml',
                'PrestaSitemapBundle_index',
                [
                    '_format' => 'xml'
                ]
            ],
            [
                '/sitemap.default.xml',
                'PrestaSitemapBundle_section',
                [
                    '_format' => 'xml',
                    'name' => 'default'
                ]
            ],
        ];
    }

    /**
     * Check if not logged in people can access routes generally available
     *
     * @covers       urls security config
     * @dataProvider allowedUrlsDataProvider
     * @security
     * @user
     * @test
     */
    public function notLoggedInAllowedUrls($expectedUrl, $urlName, $parameters = [])
    {
        $url = $this->router->generate($urlName, $parameters, false);

        // check if routes are as expected
        $this->assertEquals(
            $expectedUrl,
            $url
        );

        /**
         * @var $accountCrawler Crawler
        */
        $this->client->request('GET', $url);

        /* test status code */
        $this->assertEquals(
            Response::HTTP_OK, // 200
            $this->client->getResponse()->getStatusCode()
        );
    }

    /**
     * Data provider for test loginPage().
     *
     * @return an array of test case, array of values (urls) to check
     *   Nested arrays of values to check:
     *   - $expectedLoginRoute
     *   - $loginRoute
     */
    public function loginUrlsDataProvider()
    {
        return [
            [
                '/login',
                'fos_user_security_login'
            ]
        ];
    }

    /**
     * Check login page
     *
     * @dataProvider loginUrlsDataProvider
     * @security
     * @user
     * @test
     */
    public function loginPage($expectedLoginRoute, $loginRoute)
    {
        $loginRoute = $this->router->generate($loginRoute, [], false);

        // check if login route is as expected
        $this->assertEquals(
            $expectedLoginRoute,
            $loginRoute
        );

        // go to login page
        $loginPageCralwer = $this->client->request(
            'GET',
            $loginRoute
        );

        $this->loginPageContent($loginPageCralwer);
    }

}
