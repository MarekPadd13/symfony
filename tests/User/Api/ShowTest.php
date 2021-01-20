<?php

namespace App\Tests\User\Api;

use App\Controller\Api\UserController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShowTest extends WebTestCase
{
    const CODE_SUCCESS = 200;
    const CODE_NOTFOUND = 404;

    public function testNotFound()
    {
        $client = $this->up();
        $client->request('GET', '/api/user/4');

        $this->assertEquals(self::CODE_NOTFOUND, $client->getResponse()->getStatusCode());

        $data = $this->decodeResponseContent($client->getResponse()->getContent());
        if ($data) {
            $this->assertEquals(self::CODE_NOTFOUND, $data['status']);
            $this->assertEquals($this->getNotFoundMessage(), $data['errors']);
        }
    }

    public function testSuccess()
    {
        $client = $this->up();
        $client->request('GET', '/api/user/2');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(self::CODE_SUCCESS, $client->getResponse()->getStatusCode());
    }

    private function up()
    {
        return static::createClient();
    }

    private function decodeResponseContent($content)
    {
        $data = null;
        if ($content) {
            $data = json_decode($content, true);
        }

        return $data;
    }

    /**
     * @return string
     */
    private function getNotFoundMessage()
    {
        return self::$container->get(UserController::class)->getNotFoundMessage();
    }
}
