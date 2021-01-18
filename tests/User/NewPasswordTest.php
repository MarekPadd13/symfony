<?php

namespace App\Tests\User;

use App\Entity\User;
use App\Handler\User\NewPassword\NewPasswordHandlerInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class NewPasswordTest extends WebTestCase
{
    const EMAIL = 'marklash13@gmail.com';

    public function testSomething()
    {
        $client = $this->up();
        $user = $this->getUser();
        $client->request('GET', '/add-new-password/'.$user->getConfirmToken());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $this->getTranslatorTrans('New password'));
    }

    public function testPasswordSubmission()
    {
        $client = $this->up();
        $user = $this->getUser();
        $client->request('GET', '/add-new-password/'.$user->getConfirmToken());
        $data = [
            'reset_password[password][first]' => '28048503',
            'reset_password[password][second]' => '28048503',
        ];
        $client->submitForm($this->getTranslatorTrans('Save'), $data);
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("'.$this->getSuccessMessage().'")');
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
        return self::$container->get(NewPasswordHandlerInterface::class)->getSuccessMessage();
    }
}
