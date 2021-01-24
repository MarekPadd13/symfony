<?php

namespace App\Tests\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginTest extends WebTestCase
{
    private const LOGIN = 'markpdd13@list.ru';

    public function testSomething()
    {
        $client = $this->up();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $this->getTranslatorTrans('Security.Please sign in'));
    }

    public function testLoginSubmission()
    {
        $client = $this->up();
        $client->request('GET', '/login');
        $data = [
            'email' => self::LOGIN,
            'password' => '4r34345ye5yt4e55',
        ];
        $translator = $this->getTranslator();
        $client->submitForm($translator->trans('Security.Sign in'), $data);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testLoginSubmissionAndGetErrorStatus()
    {
        $client = $this->up();
        $client->request('GET', '/login');
        $data = [
            'email' => self::LOGIN,
            'password' => '4r34345ye5yt4e55',
        ];
        $translator = $this->getTranslator();
        $client->submitForm($translator->trans('Security.Sign in'), $data);
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("' . $this->getMessageErrorStatus() . '")');
    }

    public function testLoginSubmissionAndGetErrorEmail()
    {
        $client = $this->up();
        $client->request('GET', '/login');
        $data = [
            'email' => 'sarr@kjj.com',
            'password' => '4r34345ye5yt4e55',
        ];
        $translator = $this->getTranslator();
        $client->submitForm($translator->trans('Security.Sign in'), $data);
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("' . $this->getMessageErrorEmail() . '")');
    }

    public function testVisitingWhileLoggedIn()
    {
        $client = $this->up();
        // retrieve the test user
        $testUser = $this->getUser();
        // simulate $testUser being logged in
        $client->loginUser($testUser);
        $translator = $this->getTranslator();
        // test e.g. the profile page
        $client->request('GET', '/profile');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $translator->trans('Profile.Title'));
    }

    private function up(): KernelBrowser
    {
        return static::createClient();
    }

    private function getTranslator(): TranslatorInterface
    {
        return self::$container->get(TranslatorInterface::class);
    }

    private function getMessageErrorEmail(): string
    {
        $translator = $this->getTranslator();
        return $translator->trans('Security.Error.email');
    }

    private function getMessageErrorStatus(): string
    {
        $translator = $this->getTranslator();
        return $translator->trans('Security.Error.status');
    }

    private function getUser(): User
    {
        $userRepository = static::$container->get(UserRepository::class);

        // retrieve the test user
        return $userRepository->findOneByEmail(self::LOGIN);
    }
}
