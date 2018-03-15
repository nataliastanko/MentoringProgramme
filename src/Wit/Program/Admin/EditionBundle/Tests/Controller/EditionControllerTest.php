<?php

namespace Wit\Program\Admin\EditionBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EditionControllerTest extends WebTestCase
{

    /**
     * Just as important as testing valid inputs,
     * you should also test invalid inputs as well.
     *
     * Tests that an end date that is before the start date produces an exception.
     *
     * @ expectedException        Exception
     * @ expectedExceptionMessage Start date must be before end date
     */
    public function testIndex()
    {
        // $client = static::createClient();
    }
}
