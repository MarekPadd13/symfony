<?php

namespace App\Tests\User;

use App\Handler\User\RegistrationHandler;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationTest extends WebTestCase
{
    private const EMAIL_NEW = 'marklash13@gmail.com';

    public function testSomethingForPageLogin()
    {
        $client = $this->up();
        $client->request('GET', '/login');
        $translator = $this->getTranslator();
        $client->clickLink($translator->trans('Registration'));
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $translator->trans('Registration'));
    }

    public function testSomething()
    {
        $client = $this->up();
        $client->request('GET', '/registration');

        $this->assertResponseIsSuccessful();
        $translator = $this->getTranslator();
        $this->assertSelectorTextContains('h1', $translator->trans('Registration'));
    }

    public function testRegistrationSubmission()
    {
        $client = $this->up();
        $client->request('GET', '/registration');
        $data = [
            'register[email]' => self::EMAIL_NEW,
            'register[password][first]' => self::EMAIL_NEW,
            'register[password][second]' => self::EMAIL_NEW,
        ];
        $translator = $this->getTranslator();
        $client->submitForm($translator->trans('Save'), $data);
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("' . $this->getNoticeAddedUserMessage() . '")');
    }

    private function up(): KernelBrowser
    {
        return static::createClient();
    }

    private function getTranslator(): TranslatorInterface
    {
        return self::$container->get(TranslatorInterface::class);
    }

    private function getNoticeAddedUserMessage(): string
    {
        $translator = $this->getTranslator();
        return $translator->trans('Confirm your email');
    }
}
