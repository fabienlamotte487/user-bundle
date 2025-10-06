<?php

namespace App\Tests\Functional;

use App\DataFixtures\UserFixturesTest;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthCommandTest extends WebTestCase
{
    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    // public function testLoginSuccess(): void
    // {
        // $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
        //     'username' => 'loginSuccess@example.fr',
        //     'password' => 'Platinum#0000'
        // ]));

    //     $this->assertResponseIsSuccessful();
    //     $data = json_decode($this->client->getResponse()->getContent(), true);
    //     $this->assertArrayHasKey('token', $data);
    //     $this->assertArrayHasKey('refresh_token', $data);
    // }

    // public function testLoginFailsWithNotVerifiedEmail(): void
    // {
    //     // Tentative de login
    //     $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'username' => 'loginErrorNotVerified@example.fr',
    //         'password' => 'Platinum#0000'
    //     ]));

    //     $this->assertResponseStatusCodeSame(401);
    // }

    // public function testLoginFailsWithWrongPassword(): void
    // {
    //     $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'username' => 'loginErrorPassword@example.fr',
    //         'password' => 'WrongPassword'
    //     ]));

    //     $this->assertResponseStatusCodeSame(401);
    // }

    // public function testLoginFailsWithMissingFields(): void
    // {
    //     $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'username' => 'loginErrorPassword@example.fr',
    //         'password' => 'WrongPassword'
    //     ]));

    //     $this->assertResponseStatusCodeSame(401);
    // }
}
