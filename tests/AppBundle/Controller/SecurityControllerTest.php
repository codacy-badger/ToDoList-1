<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\User;

class SecurityControllerTest extends WebTestCase
{
    private $client;
    private $em;
    private $encoder;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testLoginAction()
    {
        $user = new User();
        $user->setUsername('testUser');
        $this->encoder = static::$kernel->getContainer()->get('security.password_encoder');
        $encodedPassword = $this->encoder->encodePassword($user, 'testPassword');
        $user->setPassword($encodedPassword);
        $user->setEmail('testEmail@test.com');
        $user->setRoles(array('ROLE_USER'));
        $this->em->persist($user);
        $this->em->flush();

        $crawler = $this->client->request('GET', '/login');
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertSame(200, $statusCode);

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'testUser';
        $form['_password'] = 'testPassword';
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('html:contains("Bienvenue sur Todo List")')->count());

        $user = $this->em->getRepository('AppBundle:User')
            ->findOneBy(['username' => 'testUser']);
        $this->em->remove($user);
        $this->em->flush();
    }

}