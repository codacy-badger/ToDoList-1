<?php

namespace Tests\AppBundle;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

trait TestTrait
{
    private $client = null;

    public function logInUser()
    {
        $session = $this->client->getContainer()->get('session');
        $firewallName = 'main';

        $token = new UsernamePasswordToken('user', 'user', $firewallName, array('ROLE_USER'));
        $session->set('_security_' . $firewallName, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}