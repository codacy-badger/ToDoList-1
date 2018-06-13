<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

class DefaultControllerTest extends WebTestCase
{
    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function login()
    {
        $session = $this->client->getContainer()->get('session');
        $firewallName = 'main';

        $token = new UsernamePasswordToken('testUser', 'testPassword', $firewallName, array('ROLE_USER'));
        $session->set('_security_' . $firewallName, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
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
        $this->login();

        $crawler = $this->client->request('GET', '/');

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertEquals(200, $statusCode);
        $this->assertContains('Consulter la liste des tâches à faire', $crawler->filter('a.btn-info')->text());
    }
}