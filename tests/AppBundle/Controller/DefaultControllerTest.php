<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testHomepageAnonymous()
    {
        $this->client->request('GET', '/');

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertEquals(302, $statusCode);
    }
}