<?php

namespace App\Tests\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class NewPasswordTest extends WebTestCase
{
    private $email = 'marklash13@gmail.com';

    public function testSomething()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/add-new-password/'.$this->getUser()->getConfirmToken());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', self::$container->get(TranslatorInterface::class)->trans('New password'));
    }

    public function testPasswordSubmission()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/add-new-password/'.$this->getUser()->getConfirmToken());
        $data = [
            'reset_password[password][first]' => '28048503',
            'reset_password[password][second]' => '28048503',
        ];
        $client->submitForm(self::$container->get(TranslatorInterface::class)->trans('Save'), $data);
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("Ok")');
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        $userRepository = static::$container->get(UserRepository::class);

        // retrieve the test user
        return $userRepository->findOneByEmail($this->email);
    }
}
