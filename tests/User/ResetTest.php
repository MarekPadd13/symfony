<?php

namespace App\Tests\User;

use App\Handler\User\Reset\ResetHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetTest extends WebTestCase
{
    public function testSomethingForPageLogin()
    {
        $client = $this->up();
        $client->request('GET', '/login');

        $client->clickLink($this->getTranslatorTrans('Reset password'));

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $this->getTranslatorTrans('Reset password'));
    }

    public function testResetSubmissionAndGetErrorEmail()
    {
        $client = $this->up();
        $client->request('GET', '/reset');
        $data = [
            'email_user[email]' => 'sarr@kjj.com',
        ];
        $client->submitForm($this->getTranslatorTrans('Reset'), $data);
        $this->assertSelectorExists('div:contains("'.$this->getErrorMessage().'")');
    }

    public function testResetSubmission()
    {
        $client = $this->up();
        $client->request('GET', '/reset');
        $data = [
            'email_user[email]' => 'marklash13@gmail.com',
        ];
        $client->submitForm($this->getTranslatorTrans('Reset'), $data);
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("'.$this->getSuccessMessage().'")');
    }

    /**
     * @return \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    private function up()
    {
        return static::createClient();
    }

    /**
     * @return string
     */
    private function getTranslatorTrans($id)
    {
        return self::$container->get(TranslatorInterface::class)->trans($id);
    }

    /**
     * @return string
     */
    private function getErrorMessage()
    {
        return self::$container->get(ResetHandlerInterface::class)->getErrorMessage();
    }

    /**
     * @return string
     */
    private function getSuccessMessage()
    {
        return self::$container->get(ResetHandlerInterface::class)->getSuccessMessage();
    }
}
