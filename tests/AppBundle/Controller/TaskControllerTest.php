<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use AppBundle\Entity\User;

class TaskControllerTest extends WebTestCase
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

    public function createTask()
    {
        $task = new Task();
        $task->setTitle('testTitle')
            ->setContent('testContent');
        $this->em->persist($task);
        $this->em->flush();
    }

    public function testListAction()
    {
        $this->login();

        $crawler = $this->client->request('GET', '/tasks');
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertSame(200, $statusCode);
        $this->assertSame(1, $crawler->filter('html:contains("Liste des tâches")')->count());
    }

    public function testCreateAction()
    {
        $this->login();
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

        $task = $this->em->getRepository('AppBundle:Task')
            ->findOneBy(['title' => 'testTitle']);
        $this->em->remove($task);
        $this->em->flush();
    }

    public function testEditAction()
    {
        $this->login();
        $this->createTask();

        $task = $this->em->getRepository('AppBundle:Task')
            ->findOneBy(['title' => 'testTitle']);
        $taskId = $task->getId();

        $crawler = $this->client->request('GET', 'tasks/' . $taskId . '/edit');
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertSame(200, $statusCode);
        $this->assertSame(1, $crawler->filter('html:contains("Modifier")')->count());

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'testTitleModified';
        $form['task[content]'] = 'testContentModified';
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertSame(200, $statusCode);
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());

        $task = $this->em->getRepository('AppBundle:Task')
            ->findOneBy(['title' => 'testTitleModified']);
        $this->em->remove($task);
        $this->em->flush();
    }

    public function testToggleTaskAction()
    {
        $this->login();
        $this->createTask();

        $task = $this->em->getRepository('AppBundle:Task')
            ->findOneBy(['title' => 'testTitle']);
        $taskId = $task->getId();

        $this->client->request('GET', 'tasks/' . $taskId . '/toggle');
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertSame(302, $statusCode);

        $crawler = $this->client->followRedirect();

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertSame(200, $statusCode);
        $this->assertSame(1, $crawler->filter('html:contains("a bien été marquée comme faite")')->count());

        $task = $this->em->getRepository('AppBundle:Task')
            ->findOneBy(['title' => 'testTitle']);
        $this->em->remove($task);
        $this->em->flush();
    }

    public function testDeleteAnonymousAction()
    {
        $this->login();
        $this->createTask();

        $task = $this->em->getRepository('AppBundle:Task')
            ->findOneBy(['title' => 'testTitle']);
        $taskId = $task->getId();

        $this->client->request('GET', 'tasks/' . $taskId . '/delete');
        $crawler = $this->client->followRedirect();
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertSame(200, $statusCode);
        $this->assertSame(1, $crawler->filter('html:contains("Superbe")')->count());
    }

    public function testDeleteAnonymousWhileNotAdminAction()
    {
        $session = $this->client->getContainer()->get('session');
        $firewallName = 'main';

        $token = new UsernamePasswordToken('user', 'user', $firewallName, array('ROLE_USER'));
        $session->set('_security_' . $firewallName, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        $this->createTask();

        $task = $this->em->getRepository('AppBundle:Task')
            ->findOneBy(['title' => 'testTitle']);
        $taskId = $task->getId();

        $this->client->request('GET', 'tasks/' . $taskId . '/delete');
        $crawler = $this->client->followRedirect();
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertSame(200, $statusCode);
        $this->assertSame(1, $crawler->filter('html:contains("Oops ! Vous devez être administrateur pour supprimer une tâche anonyme.")')->count());

        $task = $this->em->getRepository('AppBundle:Task')
            ->findOneBy(['title' => 'testTitle']);
        $this->em->remove($task);
        $this->em->flush();
    }

    public function testDeleteAuthorAction()
    {
        $this->createTask();

        $user = new User();
        $user->setUsername('testUser')
            ->setPassword('testPassword')
            ->setEmail('testEmail@test.com')
            ->setRoles(array('ROLE_USER'));

        $this->em->persist($user);
        $this->em->flush();

        $task = $this->em->getRepository('AppBundle:Task')
            ->findOneBy(['title' => 'testTitle']);
        $taskId = $task->getId();
        $task->setAuthor($user);

        $session = $this->client->getContainer()->get('session');
        $firewallName = 'main';

        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_' . $firewallName, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        $this->client->request('GET', 'tasks/' . $taskId . '/delete');
        $crawler = $this->client->followRedirect();
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertSame(200, $statusCode);
        $this->assertSame(1, $crawler->filter('html:contains("Superbe")')->count());

        $user = $this->em->getRepository('AppBundle:User')
            ->findOneBy(['username' => 'testUser']);
        $this->em->remove($user);
        $this->em->flush();
    }

    public function testDeleteBadAuthorAction()
    {
        $this->createTask();

        $author = new User();
        $author->setUsername('testUser')
            ->setPassword('testPassword')
            ->setEmail('testEmail@test.com')
            ->setRoles(array('ROLE_USER'));

        $this->em->persist($author);

        $user = new User();
        $user->setUsername('badUser')
            ->setPassword('badPassword')
            ->setEmail('badEmail@test.com')
            ->setRoles(array('ROLE_USER'));

        $this->em->persist($user);
        $this->em->flush();

        $task = $this->em->getRepository('AppBundle:Task')
            ->findOneBy(['title' => 'testTitle']);
        $taskId = $task->getId();
        $task->setAuthor($author);

        $session = $this->client->getContainer()->get('session');
        $firewallName = 'main';

        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_' . $firewallName, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        $this->client->request('GET', 'tasks/' . $taskId . '/delete');
        $crawler = $this->client->followRedirect();
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertSame(200, $statusCode);
        $this->assertSame(1, $crawler->filter('html:contains("Oops ! Vous ne pouvez supprimer que vos propres tâches.")')->count());

        $author = $this->em->getRepository('AppBundle:User')
            ->findOneBy(['username' => 'testUser']);
        $this->em->remove($author);
        $user = $this->em->getRepository('AppBundle:User')
            ->findOneBy(['username' => 'badUser']);
        $this->em->remove($user);
        $task = $this->em->getRepository('AppBundle:Task')
            ->findOneBy(['title' => 'testTitle']);
        $this->em->remove($task);
        $this->em->flush();
    }

}