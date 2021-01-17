<?php

namespace App\Tests\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetTest extends WebTestCase
{
    public function testSomethingForPageLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $client->clickLink(self::$container->get(TranslatorInterface::class)->trans('Reset password'));

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', self::$container->get(TranslatorInterface::class)->trans('Reset password'));
    }

    public function testResetSubmissionAndGetErrorEmail()
    {
        $client = static::createClient();
        $client->request('GET', '/reset');
        $data = [
            'email_user[email]' => 'sarr@kjj.com',
        ];
        $client->submitForm(self::$container->get(TranslatorInterface::class)->trans('Reset'), $data);
        $this->assertSelectorExists('div:contains("'.self::$container->get(TranslatorInterface::class)->trans('Security.Error.email').'")');
    }

    public function testResetSubmission()
    {
        $client = static::createClient();
        $client->request('GET', '/reset');
        $data = [
            'email_user[email]' => 'marklash13@gmail.com',
        ];
        $client->submitForm(self::$container->get(TranslatorInterface::class)->trans('Reset'), $data);
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("'.self::$container->get(TranslatorInterface::class)->trans('Your password reset email has been sent').'")');
    }
}
