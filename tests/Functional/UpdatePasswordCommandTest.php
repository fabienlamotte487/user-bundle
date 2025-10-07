<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UpdataPasswordCommandTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testSuccessRequestUpdatePassword(): void
    {
        $this->client->request('GET', '/reset-password', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'updateSuccess@example.fr',
        ]));
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertEquals('Vérifiez votre e-mail pour votre lien de réinitialisation.', $data['message']);
    }
}
