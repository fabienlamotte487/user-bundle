<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserApiTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    // -----------------------------------------------------
    // 3. Lecture d’un utilisateur (GET /api/user/{id})
    // -----------------------------------------------------

    // public function testGetUserSuccess(): void
    // {
    //     // Création
    //     $this->client->request('POST', '/api/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'email' => 'view@example.com',
    //         'password' => 'Password123!'
    //     ]));
    //     $data = json_decode($this->client->getResponse()->getContent(), true);
    //     $id = $data['id'];

    //     // Lecture
    //     $this->client->request('GET', '/api/user/'.$id);
    //     $this->assertResponseIsSuccessful();
    //     $data = json_decode($this->client->getResponse()->getContent(), true);
    //     $this->assertEquals('view@example.com', $data['email']);
    // }

    // public function testGetUserNotFound(): void
    // {
    //     $this->client->request('GET', '/api/user/999999');
    //     $this->assertResponseStatusCodeSame(404);
    // }

    // // -----------------------------------------------------
    // // 4. Liste des utilisateurs (GET /api/user)
    // // -----------------------------------------------------

    // public function testListUsersSuccess(): void
    // {
    //     $this->client->request('GET', '/api/user');
    //     $this->assertResponseIsSuccessful();
    //     $data = json_decode($this->client->getResponse()->getContent(), true);
    //     $this->assertIsArray($data);
    // }

    // // -----------------------------------------------------
    // // 5. Mise à jour d’un utilisateur (PUT/PATCH /api/user/{id})
    // // -----------------------------------------------------

    // public function testUpdateUserSuccess(): void
    // {
    //     // Création
    //     $this->client->request('POST', '/api/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'email' => 'update@example.com',
    //         'password' => 'Password123!'
    //     ]));
    //     $data = json_decode($this->client->getResponse()->getContent(), true);
    //     $id = $data['id'];

    //     // Update
    //     $this->client->request('PUT', '/api/user/'.$id, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'email' => 'updated@example.com'
    //     ]));

    //     $this->assertResponseIsSuccessful();
    //     $data = json_decode($this->client->getResponse()->getContent(), true);
    //     $this->assertEquals('updated@example.com', $data['email']);
    // }

    // public function testUpdateUserFailsWithInvalidEmail(): void
    // {
    //     // Création
    //     $this->client->request('POST', '/api/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'email' => 'invalid-update@example.com',
    //         'password' => 'Password123!'
    //     ]));
    //     $data = json_decode($this->client->getResponse()->getContent(), true);
    //     $id = $data['id'];

    //     // Update invalide
    //     $this->client->request('PUT', '/api/user/'.$id, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'email' => 'bad-email'
    //     ]));

    //     $this->assertResponseStatusCodeSame(400);
    // }

    // // -----------------------------------------------------
    // // 6. Suppression d’un utilisateur (DELETE /api/user/{id})
    // // -----------------------------------------------------

    // public function testDeleteUserSuccess(): void
    // {
    //     // Création
    //     $this->client->request('POST', '/api/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'email' => 'delete@example.com',
    //         'password' => 'Password123!'
    //     ]));
    //     $data = json_decode($this->client->getResponse()->getContent(), true);
    //     $id = $data['id'];

    //     // Suppression
    //     $this->client->request('DELETE', '/api/user/'.$id);
    //     $this->assertResponseStatusCodeSame(204);
    // }

    // public function testDeleteUserNotFound(): void
    // {
    //     $this->client->request('DELETE', '/api/user/999999');
    //     $this->assertResponseStatusCodeSame(404);
    // }
}
