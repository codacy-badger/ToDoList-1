<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class DataFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $task = new Task();
            $task->setTitle('task' . $i);
            $task->setContent('This is task n°' . $i);
            $manager->persist($task);
        }
        $manager->flush();
    }
}