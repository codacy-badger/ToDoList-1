<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DataFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $task = new Task();
            $task->setTitle('task' . $i);
            $task->setContent('This is task n°' . $i);
            $manager->persist($task);
        }

        $user = new User();
        $user->setUsername('user');
        $password = $this->encoder->encodePassword($user, 'user');
        $user->setPassword($password);
        $user->setEmail('user@gmail.com');
        $user->setRoles(array('ROLE_USER'));
        $manager->persist($user);

        $admin = new User();
        $admin->setUsername('admin');
        $password = $this->encoder->encodePassword($admin, 'admin');
        $admin->setPassword($password);
        $admin->setEmail('admin@gmail.com');
        $admin->setRoles(array('ROLE_ADMIN'));
        $manager->persist($admin);

        $manager->flush();
    }
}