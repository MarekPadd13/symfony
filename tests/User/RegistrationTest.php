<?php

namespace App\Tests\User;

use App\Handler\User\Registration\RegistrationHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationTest extends WebTestCase
{
    const EMAIL_NEW = 'marklash13@gmail.com';

    public function testSomethingForPageLogin()
    {
        $client = $this->up();
        $client->request('GET', '/login');

        $client->clickLink($this->getTranslatorTrans('Registration'));

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $this->getTranslatorTrans('Registration'));
    }

    public function testSomething()
    {
        $client = $this->up();
        $client->request('GET', '/registration');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $this->getTranslatorTrans('Registration'));
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
        $client->submitForm($this->getTranslatorTrans('Save'), $data);
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
     * @param $id
     *
     * @return string
     */
    private function getTranslatorTrans($id)
    {
        return self::$container->get(TranslatorInterface::class)->trans($id);
    }

    /**
     * @return string
     */
    private function getSuccessMessage()
    {
        return self::$container->get(RegistrationHandlerInterface::class)->getSuccessMessage();
    }
}
