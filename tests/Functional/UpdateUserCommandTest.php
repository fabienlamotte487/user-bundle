<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UpdateUserCommandTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    // public function testSuccessUpdateUser(): void
    // {
    //     // Connexion avec un utilisateur existant
    //     $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'username' => 'updateSuccess@example.fr',
    //         'password' => 'Platinum#0000'
    //     ]));
    //     $dataLogin = json_decode($this->client->getResponse()->getContent(), true);
    //     $token = $dataLogin['token'];

    //     // Update
    //     $this->client->request('PUT', '/api/user', [], [], [
    //         'CONTENT_TYPE' => 'application/json',
    //         'HTTP_Authorization' => 'Bearer ' . $token,
    //     ], json_encode([
    //         'pseudo' => 'Bertrand'
    //     ]));

    //     $this->assertResponseIsSuccessful();
    //     $data = json_decode($this->client->getResponse()->getContent(), true);
    //     $this->assertArrayHasKey('user', $data);
    //     $this->assertArrayHasKey('id', $data['user']);
    //     $this->assertArrayHasKey('email', $data['user']);
    //     $this->assertArrayHasKey('created_at', $data['user']);
    //     $this->assertArrayNotHasKey('plainPassword', $data['user']);
    //     $this->assertArrayNotHasKey('password', $data['user']);
        
    //     $this->assertArrayHasKey('pseudo', $data['user']);
    //     $this->assertEquals('Bertrand', $data['user']['pseudo']);
    // }

    // public function testFailWithNotVerifiedUser(): void
    // {
    //     // Connexion avec un utilisateur existant
    //     $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'username' => 'updateErrorNotVerified@example.fr',
    //         'password' => 'Platinum#0000'
    //     ]));
    //     $dataLogin = json_decode($this->client->getResponse()->getContent(), true);

    //     $this->assertResponseStatusCodeSame(401);
    //     $this->assertEquals('Email non vérifié.', $dataLogin['message']);
    // }
}
