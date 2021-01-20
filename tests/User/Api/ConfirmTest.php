<?php

namespace App\Tests\User\Api;

use App\Controller\Api\UserController;
use App\Handler\User\Confirmation\ConfirmationHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConfirmTest extends WebTestCase
{
    const CODE_SUCCESS = 200;
    const CODE_NOTFOUND = 404;
    const CODE_NOT_VALID = 422;

    public function testNotFound()
    {
        $client = $this->up();
        $client->request('PUT', '/api/confirm/5');

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
        $client->request('PUT', '/api/confirm/4');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(self::CODE_SUCCESS, $client->getResponse()->getStatusCode());

        $data = $this->decodeResponseContent($client->getResponse()->getContent());
        if ($data) {
            $this->assertEquals(self::CODE_SUCCESS, $data['status']);
            $this->assertEquals($this->getUserUpdatedMessage(), $data['success']);
        }
    }

    public function testIsActive()
    {
        $client = $this->up();
        $client->request('PUT', '/api/confirm/4');

        $this->assertEquals(self::CODE_NOT_VALID, $client->getResponse()->getStatusCode());

        $data = $this->decodeResponseContent($client->getResponse()->getContent());
        if ($data) {
            $this->assertEquals(self::CODE_NOT_VALID, $data['status']);
            $this->assertEquals($this->getErrorMessage(), $data['errors']);
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
     *
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
    private function getUserUpdatedMessage()
    {
        return self::$container->get(UserController::class)->getUserUpdatedMessage();
    }

    /**
     * @return string
     */
    private function getErrorMessage()
    {
        return self::$container->get(ConfirmationHandlerInterface::class)->getErrorMessage();
    }
}
