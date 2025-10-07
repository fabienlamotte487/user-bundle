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
