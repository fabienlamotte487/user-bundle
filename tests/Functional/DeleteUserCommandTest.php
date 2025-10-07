<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeleteUserCommandTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
    
    public function testDeleteUserSuccess(): void
    {
        // Création
        $this->client->request('POST', '/api/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'delete@example.com',
            'plainPassword' => 'Password123!',
            'pseudo'    => "Alcatraz"
        ]));

        // Connexion cet utilisateur
        $this->client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'delete@example.com',
            'password' => 'Password123!'
        ]));
        $dataLogin = json_decode($this->client->getResponse()->getContent(), true);
        $token = $dataLogin['token'];

        // Suppression
        $this->client->request(
            'DELETE', 
            '/api/user',
            [], 
            [], 
            [
                'HTTP_Authorization' => 'Bearer ' . $token,
                'CONTENT_TYPE' => 'application/json',
            ]);
        $dataDelete = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals('Utilisateur supprimé avec succès', $dataDelete['message']);
    }
}
