<?php

namespace App\Tests\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class NewPasswordTest extends WebTestCase
{
    private const EMAIL = 'marklash13@gmail.com';

    public function testSomething()
    {
        $client = $this->up();
        $user = $this->getUser();
        $client->request('GET', '/add-new-password/' . $user->getConfirmToken());
        $this->assertResponseIsSuccessful();
        $translator = $this->getTranslator();
        $this->assertSelectorTextContains('h1', $translator->trans('New password'));
    }

    public function testPasswordSubmission()
    {
        $client = $this->up();
        $user = $this->getUser();
        $client->request('GET', '/add-new-password/' . $user->getConfirmToken());
        $data = [
            'reset_password[password][first]' => '28048503',
            'reset_password[password][second]' => '28048503',
        ];
        $translator = $this->getTranslator();
        $client->submitForm($translator->trans('Save'), $data);
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("' . $this->getSuccessMessage() . '")');
    }

    private function up(): KernelBrowser
    {
        return static::createClient();
    }

    private function getTranslator(): TranslatorInterface
    {
        return self::$container->get(TranslatorInterface::class);
    }

    private function getUser(): User
    {
        $userRepository = static::$container->get(UserRepository::class);

        return $userRepository->findOneByEmail(self::EMAIL);
    }

    private function getSuccessMessage(): string
    {
        $translator = $this->getTranslator();
        return $translator->trans('Your password success upgraded');
    }
}
