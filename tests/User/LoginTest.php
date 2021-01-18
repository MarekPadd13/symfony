<?php

namespace App\Tests\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginTest extends WebTestCase
{
    const LOGIN = 'markpdd13@list.ru';

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
        $client->submitForm($this->getTranslatorTrans('Security.Sign in'), $data);
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
        $client->submitForm($this->getTranslatorTrans('Security.Sign in'), $data);
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("'.$this->getMessageErrorStatus().'")');
    }

    public function testLoginSubmissionAndGetErrorEmail()
    {
        $client = $this->up();
        $client->request('GET', '/login');
        $data = [
            'email' => 'sarr@kjj.com',
            'password' => '4r34345ye5yt4e55',
        ];
        $client->submitForm($this->getTranslatorTrans('Security.Sign in'), $data);
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("'.$this->getMessageErrorEmail().'")');
    }

    public function testVisitingWhileLoggedIn()
    {
        $client = $this->up();

        // retrieve the test user
        $testUser = $this->getUser();

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // test e.g. the profile page
        $client->request('GET', '/profile');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $this->getTranslatorTrans('Profile.Title'));
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
    private function getMessageErrorEmail()
    {
        return self::$container->get(LoginFormAuthenticator::class)->getMessageErrorEmail();
    }

    /**
     * @return string
     */
    private function getMessageErrorStatus()
    {
        return self::$container->get(LoginFormAuthenticator::class)->getMessageErrorStatus();
    }

    /**
     * @return User
     */
    private function getUser()
    {
        $userRepository = static::$container->get(UserRepository::class);

        // retrieve the test user
        return $userRepository->findOneByEmail(self::LOGIN);
    }
}
