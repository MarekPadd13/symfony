<?php

namespace App\Tests\User;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetTest extends WebTestCase
{
    public function testSomethingForPageLogin()
    {
        $client = $this->up();
        $client->request('GET', '/login');

        $translator = $this->getTranslator();
        $client->clickLink($translator->trans('Reset password'));

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $translator->trans('Reset password'));
    }

    public function testResetSubmissionAndGetErrorEmail()
    {
        $client = $this->up();
        $client->request('GET', '/reset');
        $data = [
            'email_user[email]' => 'sarr@kjj.com',
        ];
        $translator = $this->getTranslator();
        $client->submitForm($translator->trans('Reset'), $data);
        $this->assertSelectorExists('div:contains("' . $this->getErrorMessage() . '")');
    }

    public function testResetSubmission()
    {
        $client = $this->up();
        $client->request('GET', '/reset');
        $data = [
            'email_user[email]' => 'marklash13@gmail.com',
        ];
        $translator = $this->getTranslator();
        $client->submitForm($translator->trans('Reset'), $data);
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

    private function getErrorMessage(): string
    {
        $translator = $this->getTranslator();
        return $translator->trans('Security.Error.email');
    }

    private function getSuccessMessage(): string
    {
        $translator = $this->getTranslator();
        return $translator->trans('Your password reset email has been sent');
    }
}
