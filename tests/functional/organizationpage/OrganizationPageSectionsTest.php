<?php

namespace Tests\OrganizationPage;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Organization main page tests
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class OrganizationPageSectionsTest extends AbstractOrganizationPageTest
{
    /**
     * Check org menu sections
     * en locale
     *
     * @return void
     */
    public function testMenuSections(): void
    {

    }

     /**
     * check org sections info in detail
     * en locale
     *
     * @return void
     */
    public function testContentSections(): void
    {
        $client = $this->makeClient();

        // subdomain
        $domain = 'poland' . '.' . $client->getContainer()->getParameter('domain_name') . '.' . $client->getContainer()->getParameter('domain_ext');

        $client->setServerParameter('HTTP_HOST', $domain);
        $crawler = $client->request('GET', '/');
        $this->isSuccessful($client->getResponse());
        $this->assertStatusCode(200, $client);

        // check organizer section
        $this->assertSame(
            'Organizer',
            $crawler->filter('section#organizer-group #organizer h1')->text()
        );

        // org caption
        $this->assertSame(
            'Women in Technology',
            $crawler->filter('section#organizer-group .caption')->text()
        );

        // check contact section
        $this->assertSame(
            'Contact',
            $crawler->filter('section#contact-group #contact h1')->text()
        );

        // contact email address
        $this->assertContains(
            'poland@wit.tech',
            $crawler->filter('section#contact-group h4')->text()
        );
    }

}
