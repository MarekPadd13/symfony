<?php

namespace App\Tests\User\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConfirmTest extends WebTestCase
{
    private const CODE_SUCCESS = 200;
    private const CODE_NOTFOUND = 404;
    private const CODE_CONFLICT = 409;

    public function testNotFound()
    {
        $client = $this->up();
        $client->request('PUT', '/api/user/confirm/1554');

        $this->assertEquals(self::CODE_NOTFOUND, $client->getResponse()->getStatusCode());
    }

    public function testSuccess()
    {
        $client = $this->up();
        $client->request('PUT', '/api/user/confirm/10');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(self::CODE_SUCCESS, $client->getResponse()->getStatusCode());
    }

    public function testIsActive()
    {
        $client = $this->up();
        $client->request('PUT', '/api/user/confirm/9');

        $this->assertEquals(self::CODE_CONFLICT, $client->getResponse()->getStatusCode());
    }

    private function up(): KernelBrowser
    {
        return static::createClient();
    }
}
