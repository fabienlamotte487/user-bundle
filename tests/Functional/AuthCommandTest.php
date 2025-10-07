<?php

namespace App\Tests\Functional;

use App\DataFixtures\UserFixturesTest;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;


class AuthCommandTest extends WebTestCase
{
    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $client;
    protected $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();

        // Purge avant chaque test
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
        
        static::getContainer()->get(DatabaseToolCollection::class)->get()->loadFixtures([UserFixturesTest::class]);
    }

    public function testLoginSuccess(): void
    {
        $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'loginSuccess@example.fr',
            'password' => 'Platinum#0000'
        ]));

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
        $this->assertArrayHasKey('refresh_token', $data);
    }

    public function testLoginFailsWithNotVerifiedEmail(): void
    {
        // Tentative de login
        $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'loginErrorNotVerified@example.fr',
            'password' => 'Platinum#0000'
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testLoginFailsWithWrongPassword(): void
    {
        $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'loginErrorPassword@example.fr',
            'password' => 'WrongPassword'
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testLoginFailsWithMissingFields(): void
    {
        $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'loginErrorPassword@example.fr',
            'password' => 'WrongPassword'
        ]));

        $this->assertResponseStatusCodeSame(401);
    }
}
