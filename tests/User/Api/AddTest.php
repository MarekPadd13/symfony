<?php

namespace App\Tests\User\Api;

use App\Controller\Api\UserController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AddTest extends WebTestCase
{
    const CODE_SUCCESS = 200;
    const CODE_NOT_VALID = 422;

    public function testSomething()
    {
        $client = $this->up();
        $data = ['email' => 'abbddb@bbb.com', 'password' => '1555555555'];
        $client->request('POST', '/api/add', $data);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(self::CODE_SUCCESS, $client->getResponse()->getStatusCode());

        $data = $this->decodeResponseContent($client->getResponse()->getContent());
        if ($data) {
            $this->assertEquals(self::CODE_SUCCESS, $data['status']);
            $this->assertEquals($this->getUserAddedMessage(), $data['success']);
        }
    }

    public function testNotValid()
    {
        $client = $this->up();
        $data = ['email' => '', 'password' => ''];
        $client->request('POST', '/api/add', $data);

        $this->assertEquals(self::CODE_NOT_VALID, $client->getResponse()->getStatusCode());

        $data = $this->decodeResponseContent($client->getResponse()->getContent());
        if ($data) {
            $this->assertEquals(self::CODE_NOT_VALID, $data['status']);
            $this->assertEquals($this->getNotValidMessage(), $data['errors']);
        }
    }

    public function testExistsEmail()
    {
        $client = $this->up();
        $data = ['email' => 'abbb@bbb.com', 'password' => '1555555555'];
        $client->request('POST', '/api/add', $data);
        $this->assertEquals(self::CODE_NOT_VALID, $client->getResponse()->getStatusCode());

        $data = $this->decodeResponseContent($client->getResponse()->getContent());
        if ($data) {
            $this->assertEquals(self::CODE_NOT_VALID, $data['status']);
            $this->assertEquals($this->getEmailExistsMessage(), $data['errors']);
        }
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
    private function getNotValidMessage()
    {
        return self::$container->get(UserController::class)->getNotValidMessage();
    }

    /**
     * @return string
     */
    private function getEmailExistsMessage()
    {
        return self::$container->get(UserController::class)->getEmailExistsMessage();
    }

    /**
     * @return string
     */
    private function getUserAddedMessage()
    {
        return self::$container->get(UserController::class)->getUserAddedMessage();
    }
}
