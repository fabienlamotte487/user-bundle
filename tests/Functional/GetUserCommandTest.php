<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GetUserCommandTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetCurrentUserSuccess(): void 
    {
        // Connexion avec un utilisateur existant
        $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'loginSuccess@example.fr',
            'password' => 'Platinum#0000'
        ]));
        $dataLogin = json_decode($this->client->getResponse()->getContent(), true);
        $token = $dataLogin['token'];

        // Lecture de l'utilisateur connecté
        $this->client->request(
            'GET', 
            '/api/user',
            [], 
            [], 
            [
                'HTTP_Authorization' => 'Bearer ' . $token,
                'CONTENT_TYPE' => 'application/json',
            ]);

        $this->assertResponseIsSuccessful();
    }

    public function testGetTargetUserSuccess(): void
    {
        // Création
        $this->client->request('POST', '/api/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'view@example.com',
            'plainPassword' => 'Password123!',
            'pseudo' => 'jeanmachin'
        ]));
        $dataCreation = json_decode($this->client->getResponse()->getContent(), true);
        $id = $dataCreation['user']['id'];

        // Connexion avec un utilisateur existant
        $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'loginSuccess@example.fr',
            'password' => 'Platinum#0000'
        ]));
        $dataLogin = json_decode($this->client->getResponse()->getContent(), true);
        $token = $dataLogin['token'];

        // Lecture de l'utilisateur connecté
        $this->client->request(
            'GET', 
            '/api/user/' . $id,
            [], 
            [], 
            [
                'HTTP_Authorization' => 'Bearer ' . $token,
                'CONTENT_TYPE' => 'application/json',
            ]);

        $this->assertResponseIsSuccessful();
    }

    public function testGetUserNotFound(): void
    {
        
        $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'loginSuccess@example.fr',
            'password' => 'Platinum#0000'
        ]));
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $token = $data['token'];

        // Lecture
        $this->client->request('GET', '/api/user/99999', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }
}
