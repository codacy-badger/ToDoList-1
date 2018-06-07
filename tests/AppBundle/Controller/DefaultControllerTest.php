<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\TestTrait;

class DefaultControllerTest extends WebTestCase
{
    use TestTrait;

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

        $crawler = $this->client->followRedirect();

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertEquals(200, $statusCode);
        $this->assertContains('Se connecter', $crawler->filter('button')->text());
    }

    public function testHomepageLogged()
    {
        $this->logInUser();

        $crawler = $this->client->request('GET', '/');

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertEquals(200, $statusCode);
        $this->assertContains('Consulter la liste des tâches à faire', $crawler->filter('a.btn-info')->text());
    }
}