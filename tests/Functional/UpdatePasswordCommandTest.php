<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UpdatePasswordCommandTest extends WebTestCase
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

    public function testFailPasswordAlreadySent(): void
    {
        $this->client->request('GET', '/reset-password', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'updateSuccess@example.fr',
        ]));
        $this->client->request('GET', '/reset-password', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'updateSuccess@example.fr',
        ]));
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(500);
        $this->assertEquals('There was a problem handling your password reset request.', $data['message']);
        $this->assertEquals('You have already requested a reset password email. Please check your email or try again soon.', $data['reason']);
    }

    public function testFailEmailNotFound(): void
    {
        $this->client->request('GET', '/reset-password', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'doesnotexist@example.fr',
        ]));

        $this->assertResponseStatusCodeSame(404);
    }
}
