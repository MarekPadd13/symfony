<?php

namespace App\Tests\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfirmTest extends WebTestCase
{
    private $email = 'marklash13@gmail.com';

    public function testConfirmOk()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/confirm/'.$this->getUser()->getConfirmToken());

        $client->followRedirect();
        $this->assertSelectorExists('div:contains("Ok")');
    }

    public function testConfirmError()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/confirm/'.$this->getUser()->getConfirmToken());

        $client->followRedirect();
        $this->assertSelectorExists('div:contains("'.self::$container->get(TranslatorInterface::class)->trans('Your status is active').'")');
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
