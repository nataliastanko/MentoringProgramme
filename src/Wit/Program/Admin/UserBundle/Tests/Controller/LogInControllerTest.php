<?php

namespace Wit\Program\Admin\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Wit\SiteBundle\Tests\Controller\AbstractControllerTest;
use Wit\Program\Account\UserBundle\Entity\User;

/**
 * @author  Natalia Stanko <contact@nataliastanko.com>
 * @license MIT License (MIT)
 *
 * Log in
 * Test security when logged in
 */
class LogInControllerTest extends AbstractControllerTest
{
    private $accountUrl;

    public function setUp()
    {
        parent::setUp();

        $this->accountUrl = $this->router->generate('account', [], false); // '/account/'
    }

    /**
     * @security
     * @user
     * @test
     */
    public function securedAccount()
    {
        /**
         * test access when not logged in
        */
        $this->accessAccountWhenNotloggedIn();

        // log in
        $user = $this->logIn('abc@def.ghx');

        /**
         * test access when logged in
        */
        $crawler = $this->client->request(
            'GET',
            $this->accountUrl
        );

        $this->assertSame(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );

        $this->assertSame(
            'Hello abc!',
            $crawler->filter('h1.h1')->text()
        );
    }

    private function accessAccountWhenNotloggedIn()
    {
        $crawler = $this->client->request(
            'GET',
            $this->accountUrl
        );
        // Symfony profiler: 302 Redirect from @account
        $this->assertSame(
            Response::HTTP_FOUND, // 302
            $this->client->getResponse()->getStatusCode()
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());
    }

    /**
     * Check logout mechanism
     *
     * @security
     * @user
     * @test
     */
    public function logout()
    {
        $this->logIn('abc@def.ghx');

        $session = $this->client->getContainer()->get('session');
        $this->assertTrue($session->has('_security_main'));

        // check if logout route is as expected
        $this->assertEquals(
            '/logout',
            $this->router->generate('fos_user_security_logout', [], false)
        );

        ;
        $this->client->request(
            'GET',
            $this->router->generate('fos_user_security_logout', [], false)
        );

        /**
 * test access when not logged in
*/
        $this->accessAccountWhenNotloggedIn();

        // check session after log out
        $session = $this->client->getContainer()->get('session');
        $this->assertFalse($session->has('_security_main'));
    }

    /**
     * Check login pages international
     *
     * @TODO
     * @security
     * @user
     * @test
     * @multilanguage
     */
    public function multilanguage()
    {
        $this->markTestIncomplete(
            'Strona logowania w różnych językach'
        );
    }

    /**
     * Travers through login page
     *
     * @param Crawler $crawler actual page crawler
     */
    protected function loginPageContent($crawler)
    {
        // check page name
        $this->assertContains(
            $this->translator->trans('page.login'), // traversing
            $crawler
                ->filter('h1')
                ->text()
        );

        // check form log in button
        $this->assertContains(
            // $this->translator->trans('security.login.submit'),
            $this->translator->trans('security.login.submit', [], 'FOSUserBundle'),
            $crawler
                ->filter('form')
                ->filter('button[type=submit]')
                ->text()
        );
    }

    /**
     * @see http://symfony.com/doc/current/testing/http_authentication.html
     * The trick now is to bypass the authentication process,
     * create the authentication token yourself and store it in the session.
     *
     * @return User logged user
     */
    protected function logIn($email)
    {
        // create session id
        $this->client->request('GET', '/');

        // $context = '_security_main_context'; // custom security context name defined
        $context = '_security_main';
        $provider = 'main';

        $user = $this->em->getRepository('WitProgramAccountUserBundle:User')
            ->findOneByEmail($email);

        if (!($user instanceof User)) {
            $this->assertTrue(
                false,
                'Give a valid user in order to test it'
            );
        }

        $token = new UsernamePasswordToken($user, null, $provider, $user->getRoles());

        $this->client->getContainer()->get('security.token_storage')->setToken($token); //now the user is logged in
        $session = $this->client->getContainer()->get('session');
        $session->set($context, serialize($token)); // when custom security context name defined
        $session->save();

        // save cookie seesion id
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        return $user;
    }

}
