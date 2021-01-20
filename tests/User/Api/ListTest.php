<?php

namespace App\Tests\User\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ListTest extends WebTestCase
{
    const CODE_SUCCESS = 200;

    public function testSomething()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(self::CODE_SUCCESS, $client->getResponse()->getStatusCode());
    }
}
