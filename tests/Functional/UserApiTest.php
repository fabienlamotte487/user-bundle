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
    // 1. Création d’utilisateur (POST /api/user)
    // -----------------------------------------------------

    // Test classique avec des données valide => 201 user created
    public function testCreateUserSuccess(): void
    {
        $this->client->request('POST', '/api/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            "email" => "test@example.com",
            "plainPassword" => "Password123!",
            "pseudo" => "Fabien"
        ]));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data['user']);
        $this->assertEquals('test@example.com', $data['user']['email']);
        $this->assertEquals('Fabien', $data['user']['pseudo']);
        $this->assertArrayNotHasKey('plainPassword', $data['user']);
        $this->assertArrayNotHasKey('password', $data['user']);
    }

    // Test avec email existant => 400 bad request same email
    public function testCreateUserFailsWithDuplicateEmail(): void
    {
        // Précondition : créer un user
        $this->client->request('POST', '/api/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            "email" => "duplicate@example.com",
            "plainPassword" => "Password123!",
            "pseudo" => "Fabien"
        ]));

        // Deuxième création avec le même email
        $this->client->request('POST', '/api/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            "email" => "duplicate@example.com",
            "plainPassword" => "Password123!",
            "pseudo" => "Fabien"
        ]));

        $this->assertResponseStatusCodeSame(400);
    }

    // Test avec email invalide => 400 bad request
    public function testCreateUserFailsWithInvalidEmail(): void
    {
        $this->client->request('POST', '/api/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'not-an-email',
            "plainPassword" => "Password123!",
            "pseudo" => "Fabien"
        ]));

        $this->assertResponseStatusCodeSame(400);
    }

    // Test avec mot de passe faible => 400 bad request
    public function testCreateUserFailsWithWeakPassword(): void
    {
        $this->client->request('POST', '/api/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'weakpass@example.com',
            "pseudo" => "Fabien",
            'plainPassword' => '123'
        ]));

        $this->assertResponseStatusCodeSame(400);
    }

    // -----------------------------------------------------
    // 2. Lecture d’un utilisateur (GET /api/user/{id})
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
    // // 3. Liste des utilisateurs (GET /api/user)
    // // -----------------------------------------------------

    // public function testListUsersSuccess(): void
    // {
    //     $this->client->request('GET', '/api/user');
    //     $this->assertResponseIsSuccessful();
    //     $data = json_decode($this->client->getResponse()->getContent(), true);
    //     $this->assertIsArray($data);
    // }

    // // -----------------------------------------------------
    // // 4. Mise à jour d’un utilisateur (PUT/PATCH /api/user/{id})
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
    // // 5. Suppression d’un utilisateur (DELETE /api/user/{id})
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

    // // -----------------------------------------------------
    // // 6. Authentification (POST /api/login_check)
    // // -----------------------------------------------------

    // public function testLoginSuccess(): void
    // {
    //     // Créer un utilisateur
    //     $this->client->request('POST', '/api/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'email' => 'login@example.com',
    //         'password' => 'Password123!'
    //     ]));

    //     // Tentative de login
    //     $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'username' => 'login@example.com',
    //         'password' => 'Password123!'
    //     ]));

    //     $this->assertResponseIsSuccessful();
    //     $data = json_decode($this->client->getResponse()->getContent(), true);
    //     $this->assertArrayHasKey('token', $data);
    // }

    // public function testLoginFailsWithWrongPassword(): void
    // {
    //     $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
    //         'username' => 'login@example.com',
    //         'password' => 'WrongPassword'
    //     ]));

    //     $this->assertResponseStatusCodeSame(401);
    // }
}
