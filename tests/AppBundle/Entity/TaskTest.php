<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskTest extends WebTestCase
{
    public function testTask()
    {
        $task = new Task();
        $task->setTitle('Test de tâches');
        $task->setContent('Test de tâches');
        $author = new User();
        $task->setAuthor($author);
        $date = new \DateTime();
        $task->setCreatedAt($date);
        $task->setIsDone(true);

        $this->assertEquals(null, $task->getId());
        $this->assertEquals('Test de tâches', $task->getTitle());
        $this->assertEquals('Test de tâches', $task->getContent());
        $this->assertEquals($author, $task->getAuthor());
        $this->assertEquals($date, $task->getCreatedAt());
        $this->assertEquals(true, $task->getIsDone());

        $task->toggle('testFlag');
        $this->assertEquals('testFlag', $task->isDone());
    }
}