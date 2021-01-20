<?php

namespace App\Tests\User\Api;

use App\Controller\Api\UserController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeleteTest extends WebTestCase
{
    const CODE_SUCCESS = 200;
    const CODE_NOTFOUND = 404;

    public function testNotFound()
    {
        $client = $this->up();
        $client->request('DELETE', '/api/delete/4');

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
        $client->request('DELETE', '/api/delete/3');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(self::CODE_SUCCESS, $client->getResponse()->getStatusCode());

        $data = $this->decodeResponseContent($client->getResponse()->getContent());
        if ($data) {
            $this->assertEquals(self::CODE_SUCCESS, $data['status']);
            $this->assertEquals($this->getUserDeletedMessage(), $data['success']);
        }
    }

    /**
     * @return \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    private function up()
    {
        return static::createClient();
    }

    /**
     * @param $content
     * @return mixed|null
     */
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

    /**
     * @return string
     */
    private function getUserDeletedMessage()
    {
        return self::$container->get(UserController::class)->getUserDeletedMessage();
    }
}
