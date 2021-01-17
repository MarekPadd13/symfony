<?php

namespace App\Tests\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationTest extends WebTestCase
{
    private $emailNew = 'marklash13@gmail.com';

    public function testSomethingForPageLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $client->clickLink(self::$container->get(TranslatorInterface::class)->trans('Registration'));

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', self::$container->get(TranslatorInterface::class)->trans('Registration'));
    }

    public function testSomething()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/registration');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', self::$container->get(TranslatorInterface::class)->trans('Registration'));
    }

    public function testRegistrationSubmission()
    {
        $client = static::createClient();
        $client->request('GET', '/registration');
        $data = [
            'register[email]' => $this->emailNew,
            'register[password][first]' => $this->emailNew,
            'register[password][second]' => $this->emailNew,
        ];
        $client->submitForm(self::$container->get(TranslatorInterface::class)->trans('Save'), $data);
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("'.self::$container->get(TranslatorInterface::class)->trans('Confirm your email').'")');
    }

    public function testConfirmForEmail()
    {
        $client = static::createClient();
        $client->request('GET', '/confirm');
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("'.self::$container->get(TranslatorInterface::class)->trans('Confirm your email').'")');
    }
}
