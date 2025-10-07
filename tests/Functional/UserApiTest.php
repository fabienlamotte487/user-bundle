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
}
