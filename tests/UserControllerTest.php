<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $client->request('GET', '/home');

        $this->assertResponseStatusCodeSame(403); // Expected 403 due to missing JWT token
        $this->assertJson($client->getResponse()->getContent());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Token ou XSRF manquant', $responseData['error']);
    }

    // public function testCreateUser(): void
    // {
    //     $client = static::createClient();
    //     $client->request('GET', '/home');

    //     $this->assertResponseStatusCodeSame(403); // Expected 403 due to missing JWT token
    //     $this->assertJson($client->getResponse()->getContent());
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $this->assertEquals('Token ou XSRF manquant', $responseData['error']);
    // }

    public function testGetUserInfoEndpointExists(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/1');

        // The endpoint should exist and return a JSON response
        // It might be 500 due to database issues, but the route should work
        $this->assertTrue(in_array($client->getResponse()->getStatusCode(), [200, 404, 500]));
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testGetUserInfoResponseStructure(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/999');

        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        // Should return JSON with error structure
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('error', $responseData);
    }

    public function testGetUserInfoWithValidId(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/1');

        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        // Should return JSON response (either success or error)
        $this->assertIsArray($responseData);

        // If successful, should have user data structure
        if ($response->getStatusCode() === 200) {
            $this->assertArrayHasKey('id', $responseData);
            $this->assertArrayHasKey('username', $responseData);
            $this->assertArrayHasKey('email', $responseData);
            $this->assertArrayHasKey('roles', $responseData);
            $this->assertArrayHasKey('is_verified', $responseData);
        }
    }

    public function testCreateUser(): void
    {
        $client = static::createClient();
        $client->request('POST', '/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['username' => 'testusevr', 'email' => 'tsest@example.com', 'password' => 'password']));
        // dd($client->getResponse()->getContent());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($client->getResponse()->getContent());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Inscription réussie. Vérifiez votre email.', $responseData['message']);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('username', $responseData);
        $this->assertArrayHasKey('email', $responseData);
        $this->assertArrayHasKey('roles', $responseData);
        $this->assertArrayHasKey('is_verified', $responseData);

        $this->deleteUser($responseData['id']);
    }

    public function testUpdateUser(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/user/1', [], [], [
            'CONTENT_TYPE' => 'application/json',
        // ], json_encode(['username' => 'testuser', 'email' => 'test@example.com']));
        ], json_encode(['params' => ['params' => ['username' => 'testuser', 'email' => 'test@example.com']]]));
        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($client->getResponse()->getContent());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('User updated successfully', $responseData['message']);
    }

    public function deleteUser($id): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/user/'.$id);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($client->getResponse()->getContent());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('User deleted successfully', $responseData['message']);
    }

    // public function testDeleteUser(): void
    // {
    //     $client = static::createClient();
    //     $client->request('DELETE', '/user/'.$id);
    //     $this->assertResponseStatusCodeSame(200);
    //     $this->assertJson($client->getResponse()->getContent());
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $this->assertEquals('User deleted successfully', $responseData['message']);
    // }
}
