<?php

namespace App\Tests\User;

use App\Entity\User;
use App\Handler\User\ConfirmationHandler;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfirmTest extends WebTestCase
{
    private const EMAIL = 'marklash13@gmail.com';

    public function testConfirmOk()
    {
        $client = $this->up();
        $user = $this->getUser();
        $client->request('GET', '/confirm/' . $user->getConfirmToken());

        $client->followRedirect();
        $this->assertSelectorExists('div:contains("' . $this->getSuccessMessage() . '")');
    }

    public function testConfirmError()
    {
        $client = $this->up();
        $user = $this->getUser();
        $client->request('GET', '/confirm/' . $user->getConfirmToken());

        $client->followRedirect();
        $this->assertSelectorExists('div:contains("' . $this->getErrorMessage() . '")');
    }

    private function up(): KernelBrowser
    {
        return static::createClient();
    }

    private function getUser(): User
    {
        $userRepository = static::$container->get(UserRepository::class);

        // retrieve the test user
        return $userRepository->findOneByEmail(self::EMAIL);
    }

    private function getSuccessMessage(): string
    {
        return self::$container->get(TranslatorInterface::class)->trans('Your email is confirmed');
    }

    private function getErrorMessage(): string
    {
        return self::$container->get(TranslatorInterface::class)->trans('Your status is active');
    }
}
