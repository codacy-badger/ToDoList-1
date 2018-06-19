<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    public function testUser()
    {
        $user = new User();

        $user->setUsername('testUser')
            ->setPassword('testPassword')
            ->setEmail('testEmail@test.com')
            ->setRoles('ROLE_USER');

        $this->assertEquals(null, $user->getId());
        $this->assertEquals('testUser', $user->getUsername());
        $this->assertEquals('testPassword', $user->getPassword());
        $this->assertEquals('testEmail@test.com', $user->getEmail());
        $this->assertEquals('ROLE_USER', $user->getRoles());
        $this->assertEquals(null, $user->getSalt());
    }
}