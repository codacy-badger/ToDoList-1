<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\TestTrait;

class TaskControllerTest extends WebTestCase
{
    use TestTrait;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testListAction()
    {

    }
}