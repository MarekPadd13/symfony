<?php

namespace App\Tests\User;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginTest extends WebTestCase
{
    private $email = 'markpdd13@list.ru';

    public function testSomething()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', self::$container->get(TranslatorInterface::class)->trans('Security.Please sign in'));
    }

    public function testLoginSubmission()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $data = [
            'email' => $this->email,
            'password' => '4r34345ye5yt4e55',
        ];
        $client->submitForm(self::$container->get(TranslatorInterface::class)->trans('Security.Sign in'), $data);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testLoginSubmissionAndGetErrorStatus()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $data = [
            'email' => $this->email,
            'password' => '4r34345ye5yt4e55',
        ];
        $client->submitForm(self::$container->get(TranslatorInterface::class)->trans('Security.Sign in'), $data);
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("'.self::$container->get(TranslatorInterface::class)->trans('Security.Error.status').'")');
    }

    public function testLoginSubmissionAndGetErrorEmail()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $data = [
            'email' => 'sarr@kjj.com',
            'password' => '4r34345ye5yt4e55',
        ];
        $client->submitForm(self::$container->get(TranslatorInterface::class)->trans('Security.Sign in'), $data);
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("'.self::$container->get(TranslatorInterface::class)->trans('Security.Error.email').'")');
    }

    public function testVisitingWhileLoggedIn()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail($this->email);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // test e.g. the profile page
        $client->request('GET', '/profile');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', self::$container->get(TranslatorInterface::class)->trans('Profile.Title'));
    }
}
