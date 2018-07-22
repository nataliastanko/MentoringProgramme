<?php

namespace Tests\OrganizationPage;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class OrganizationPageTest extends AbstractOrganizationPageTest
{
    /**
     * Organization main page test
     * check general information displayed on the page
     * en locale
     *
     * request(
        $method,
        $uri,
        array $parameters = array(),
        array $files = array(),
        array $server = array(),
        $content = null,
        $changeHistory = true
        )
     *
     * @return void
     */
    public function testMainPage(): void
    {
        $client = $this->makeClient();

        // subdomain
        $domain = 'poland' . '.' . $client->getContainer()->getParameter('domain_name') . '.' . $client->getContainer()->getParameter('domain_ext');

        $client->setServerParameter('HTTP_HOST', $domain);
        $crawler = $client->request('GET', '/');
        $this->isSuccessful($client->getResponse());
        $this->assertStatusCode(200, $client);

        // There is one <h1> in .bglead
        $this->assertSame(
            1,
            $crawler->filter('.bglead > h1')->count()
        );

        // check head .bglead h1 text
        $this->assertSame(
            'Tech Leaders',
            $crawler->filter('.bglead > h1')->text()
        );

        // check subhead .bglead h2 text
        $this->assertSame(
            strtoupper('poland'),
            $crawler->filter('.bglead > h2')->text()
        );

        // en
        // check subhead .bglead h3 text
        $this->assertSame(
            'Mentoring program for women',
            $crawler->filter('.bglead > h3')->text()
        );

        // there are exactly 2 h1 tags on the page sections
        // when org is fresh & empty
        $this->assertCount(2, $crawler->filter('.container section h1'));
    }
}
