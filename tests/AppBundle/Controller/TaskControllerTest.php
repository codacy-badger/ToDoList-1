<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\TestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    use TestTrait;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testListAction()
    {
        $this->logInUser();

        $crawler = $this->client->request('GET', '/tasks');
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertSame(200, $statusCode);
        $this->assertSame(1, $crawler->filter('html:contains("Liste des tâches")')->count());
    }
}