<?php

namespace App\Tests\Acceptance;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class PingTest extends ApiTestCase
{
    public function testSomething(): void
    {
        $response = static::createClient()->request('GET', '/api/ping');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['status' => 'OK']);
    }
}
