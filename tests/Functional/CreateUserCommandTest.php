<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateUserCommandTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    // Test classique avec des données valide => 201 user created
    public function testCreateUserSuccess(): void
    {
        $payload = json_encode([
            "email" => "test@example.com",
            "plainPassword" => "Password123!",
            "pseudo" => "Fabien"
        ]);

        // Appel de l'API
        $this->client->request(
            'POST',
            '/api/user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload
        );

        // Vérifie le code HTTP
        $this->assertResponseStatusCodeSame(201);
        
        // Vérifie le contenu JSON
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('user', $data);
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

    public function testCreateUserFailsWithEmptyFields(): void
    {
        $this->client->request('POST', '/api/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => '',
            'plainPassword' => '',
            "pseudo" => "",
        ]));

        $this->assertResponseStatusCodeSame(400);
    }
}
