<?php

namespace App\Tests\User;

use App\Entity\User;
use App\Handler\User\Confirmation\ConfirmationHandlerInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConfirmTest extends WebTestCase
{
    const EMAIL = 'marklash13@gmail.com';

    public function testConfirmOk()
    {
        $client = $this->up();
        $user = $this->getUser();
        $client->request('GET', '/confirm/'.$user->getConfirmToken());

        $client->followRedirect();
        $this->assertSelectorExists('div:contains("'.$this->getSuccessMessage().'")');
    }

    public function testConfirmError()
    {
        $client = $this->up();
        $user = $this->getUser();
        $client->request('GET', '/confirm/'.$user->getConfirmToken());

        $client->followRedirect();
        $this->assertSelectorExists('div:contains("'.$this->getErrorMessage().'")');
    }

    /**
     * @return \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    private function up()
    {
        return static::createClient();
    }

    /**
     * @return User
     */
    private function getUser()
    {
        $userRepository = static::$container->get(UserRepository::class);

        // retrieve the test user
        return $userRepository->findOneByEmail(self::EMAIL);
    }

    /**
     * @return string
     */
    private function getSuccessMessage()
    {
        return self::$container->get(ConfirmationHandlerInterface::class)->getSuccessMessage();
    }

    /**
     * @return string
     */
    private function getErrorMessage()
    {
        return self::$container->get(ConfirmationHandlerInterface::class)->getErrorMessage();
    }
}
