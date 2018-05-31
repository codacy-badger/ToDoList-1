<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    public function testUser()
    {
        $user = new User();

        $user->setUsername('userTest');
        $user->setPassword('passwordTest');
        $user->setEmail('emailTest@test.com');
        $user->setRoles('ROLE_USER');

        $this->assertEquals(null, $user->getId());
        $this->assertEquals('userTest', $user->getUsername());
        $this->assertEquals('passwordTest', $user->getPassword());
        $this->assertEquals('emailTest@test.com', $user->getEmail());
        $this->assertEquals('ROLE_USER', $user->getRoles());
        $this->assertEquals(null, $user->getSalt());
    }
}