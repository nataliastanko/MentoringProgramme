<?php

namespace Tests\LandingPageBundle\Controller;

// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class LandingPageControllerTest extends WebTestCase
{
    public function testOrganizationList(): void
    {
        // $this->verbosityLevel = 'debug';
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/');
        $this->assertStatusCode(200, $client);

        // $form = $crawler->selectButton('Submit')->form();
        // $crawler = $client->submit($form);

        // // We should get a validation error for the empty fields.
        // $this->assertStatusCode(200, $client);
        // $this->assertValidationErrors(['data.email', 'data.message'], $client->getContainer());

        // // Try again with with the fields filled out.
        // $form = $crawler->selectButton('Submit')->form();
        // $form->setValues(['contact[email]' => 'nobody@example.com', 'contact[message]' => 'Hello']);
        // $client->submit($form);
        // $this->assertStatusCode(302, $client);
    }
}
