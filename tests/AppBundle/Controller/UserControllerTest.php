<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

class UserControllerTest extends WebTestCase
{
    private $client;
    private $em;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    public function login()
    {
        $session = $this->client->getContainer()->get('session');
        $firewallName = 'main';

        $token = new UsernamePasswordToken('user', 'user', $firewallName, array('ROLE_ADMIN'));
        $session->set('_security_' . $firewallName, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    public function testListAction()
    {
        $this->login();
        $crawler = $this->client->request('GET', '/users');
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertSame(200, $statusCode);
        $this->assertSame(1, $crawler->filter('html:contains("Liste des utilisateurs")')->count());
    }

}