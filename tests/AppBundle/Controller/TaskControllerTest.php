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

    public function testCreateAction()
    {
        $this->logInUser();
        $crawler = $this->client->request('GET', '/tasks/create');
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertSame(200, $statusCode);
        $this->assertSame(1, $crawler->filter('html:contains("Créer une tâche")')->count());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'testTitle';
        $form['task[content]'] = 'testContent';
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertSame(200, $statusCode);
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
    }
}