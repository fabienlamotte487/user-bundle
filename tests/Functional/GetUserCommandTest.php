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

    // public function testSuccessGetCurrentUser(): void 
    // {
    //     // Connexion avec un utilisateur existant
    //     $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'username' => 'loginSuccess@example.fr',
    //         'password' => 'Platinum#0000'
    //     ]));
    //     $dataLogin = json_decode($this->client->getResponse()->getContent(), true);
    //     $token = $dataLogin['token'];

    //     // Lecture de l'utilisateur connecté
    //     $this->client->request(
    //         'GET', 
    //         '/api/user',
    //         [], 
    //         [], 
    //         [
    //             'HTTP_Authorization' => 'Bearer ' . $token,
    //             'CONTENT_TYPE' => 'application/json',
    //         ]);
    //     $dataUser = json_decode($this->client->getResponse()->getContent(), true);
            
    //     $this->assertArrayHasKey('user', $dataUser);
    //     $this->assertArrayHasKey('id', $dataUser['user']);
    //     $this->assertArrayHasKey('email', $dataUser['user']);
    //     $this->assertArrayHasKey('pseudo', $dataUser['user']);
    //     $this->assertArrayHasKey('created_at', $dataUser['user']);
    //     $this->assertArrayHasKey('isVerified', $dataUser['user']);
    //     $this->assertArrayNotHasKey('plainPassword', $dataUser['user']);
    //     $this->assertArrayNotHasKey('password', $dataUser['user']);

    //     $this->assertResponseIsSuccessful();
    // }

    // public function testSuccessGetTargetUser(): void
    // {
    //     // Création
    //     $this->client->request('POST', '/api/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'email' => 'view@example.com',
    //         'plainPassword' => 'Password123!',
    //         'pseudo' => 'jeanmachin'
    //     ]));
    //     $dataCreation = json_decode($this->client->getResponse()->getContent(), true);
    //     $id = $dataCreation['user']['id'];

    //     // Connexion avec un utilisateur existant
    //     $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'username' => 'loginSuccess@example.fr',
    //         'password' => 'Platinum#0000'
    //     ]));
    //     $dataLogin = json_decode($this->client->getResponse()->getContent(), true);
    //     $token = $dataLogin['token'];

    //     // Lecture de l'utilisateur connecté
    //     $this->client->request(
    //         'GET', 
    //         '/api/user/' . $id,
    //         [], 
    //         [], 
    //         [
    //             'HTTP_Authorization' => 'Bearer ' . $token,
    //             'CONTENT_TYPE' => 'application/json',
    //         ]);

    //     $dataUser = json_decode($this->client->getResponse()->getContent(), true);
    //     $this->assertArrayHasKey('user', $dataUser);
    //     $this->assertArrayHasKey('id', $dataUser['user']);
    //     $this->assertArrayHasKey('email', $dataUser['user']);
    //     $this->assertArrayHasKey('pseudo', $dataUser['user']);
    //     $this->assertArrayHasKey('created_at', $dataUser['user']);
    //     $this->assertArrayNotHasKey('plainPassword', $dataUser['user']);
    //     $this->assertArrayNotHasKey('password', $dataUser['user']);

    //     $this->assertResponseIsSuccessful();
    // }

    // public function testFailGetUserNotFound(): void
    // {
        
    //     $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'username' => 'loginSuccess@example.fr',
    //         'password' => 'Platinum#0000'
    //     ]));
    //     $data = json_decode($this->client->getResponse()->getContent(), true);
    //     $token = $data['token'];

    //     // Lecture
    //     $this->client->request('GET', '/api/user/99999', [], [], [
    //         'HTTP_Authorization' => 'Bearer ' . $token,
    //         'CONTENT_TYPE' => 'application/json',
    //     ]);

    //     $this->assertResponseStatusCodeSame(404);
    // }
    
    // public function testFailGetUnverifiedUser(): void
    // {
    //     $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'username' => 'loginErrorNotVerified@example.fr',
    //         'password' => 'Platinum#0000'
    //     ]));
    //     $data = json_decode($this->client->getResponse()->getContent(), true);
    //     $token = $data['token'];

    //     // Lecture
    //     $this->client->request('GET', '/api/user', [], [], [
    //         'HTTP_Authorization' => 'Bearer ' . $token,
    //         'CONTENT_TYPE' => 'application/json',
    //     ]);

    //     $this->assertResponseStatusCodeSame(401);
    // }
    
    // public function testFailGetUserWithoutToken(): void
    // {
    //     $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'username' => 'loginErrorNotVerified@example.fr',
    //         'password' => 'Platinum#0000'
    //     ]));
    //     $data = json_decode($this->client->getResponse()->getContent(), true);

    //     // Lecture
    //     $this->client->request('GET', '/api/user', [], [], [
    //         'CONTENT_TYPE' => 'application/json',
    //     ]);

    //     $this->assertResponseStatusCodeSame(401);
    // }
}
