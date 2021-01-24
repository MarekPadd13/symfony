<?php

namespace App\Tests\User\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeleteTest extends WebTestCase
{
    const CODE_SUCCESS = 200;
    const CODE_NOTFOUND = 404;

    public function testNotFound()
    {
        $client = $this->up();
        $client->request('DELETE', '/api/user/delete/144');

        $this->assertEquals(self::CODE_NOTFOUND, $client->getResponse()->getStatusCode());
    }

    public function testSuccess()
    {
        $client = $this->up();
        $client->request('DELETE', '/api/user/delete/7');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(self::CODE_SUCCESS, $client->getResponse()->getStatusCode());
    }

    private function up(): KernelBrowser
    {
        return static::createClient();
    }
}
