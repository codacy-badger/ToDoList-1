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
        $task->setTitle('testTitle');
        $task->setContent('testContent');
        $author = new User();
        $task->setAuthor($author);
        $date = new \DateTime();
        $task->setCreatedAt($date);
        $task->setIsDone(true);

        $this->assertEquals(null, $task->getId());
        $this->assertEquals('testTitle', $task->getTitle());
        $this->assertEquals('testContent', $task->getContent());
        $this->assertEquals($author, $task->getAuthor());
        $this->assertEquals($date, $task->getCreatedAt());
        $this->assertEquals(true, $task->getIsDone());

        $task->toggle('testFlag');
        $this->assertEquals('testFlag', $task->isDone());
    }
}