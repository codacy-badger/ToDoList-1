<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testLoginAction()
    {
        $crawler = $this->client->request('GET', '/login');
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertSame(200, $statusCode);
    }

}