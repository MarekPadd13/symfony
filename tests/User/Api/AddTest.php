<?php

namespace App\Tests\User\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AddTest extends WebTestCase
{
    private const CODE_SUCCESS = 200;
    private const CODE_NOT_VALID = 422;
    private const CODE_NOT_CONTENT = 204;

    public function testSomething()
    {
        $client = $this->up();
        $data = '{"email":"marekpdadd1f2@log.ru", "password" : { "first": "54554554444", "second": "54554554444"}}';

        $client->request(
            'POST',
            '/api/user/add',
            [],
            [],
            [],
            $data
        );

        $this->assertResponseIsSuccessful();
        $this->assertEquals(self::CODE_SUCCESS, $client->getResponse()->getStatusCode());
    }

    public function testNotContent()
    {
        $client = $this->up();
        $client->request('POST', '/api/user/add');

        $this->assertEquals(self::CODE_NOT_CONTENT, $client->getResponse()->getStatusCode());
    }

    public function testNotValid()
    {
        $client = $this->up();
        $data = '{"email":"marekpdadd12@log.ru", "password" : { "first": "54554554444", "second": "54554554444"}}';

        $client->request(
            'POST',
            '/api/user/add',
            [],
            [],
            [],
            $data
        );
        $this->assertEquals(self::CODE_NOT_VALID, $client->getResponse()->getStatusCode());
    }

    private function up(): KernelBrowser
    {
        return static::createClient();
    }
}
