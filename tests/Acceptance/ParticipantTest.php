<?php

namespace App\Tests\Acceptance;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\Uid\Uuid;

class ParticipantTest extends ApiTestCase
{
    public function testParticipantCreationShouldBeOK(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/tournaments', [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => json_encode(['name' => 'Tournament'])
        ]);
        $this->assertResponseStatusCodeSame(200);
        $tournamentId = $client->getResponse()->toArray()["id"];

        $client->request('POST', "/api/tournaments/$tournamentId/participants", [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => json_encode([
                "name"  => "Novak Djokovic",
                "elo"   => 2500,
                "tounamentId" => $tournamentId
            ])
        ]);

        $this->assertResponseIsSuccessful();
        $response = $client->getResponse()->toArray();
        $this->assertIsString($response["id"]);
    }

    public function testParticipantTournamentNotExists(): void
    {
        $client = static::createClient();
        $id = Uuid::v4();
        $client->request('POST', "/api/tournaments/$id/participants", [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => json_encode([
                "name"  => "Novak Djokovic",
                "elo"   => 2500
            ])
        ]);

        $this->assertResponseStatusCodeSame(404, "Tournament doesn't exists");
    }

    public function testParticipantCreationWithNoELOShouldNotWork(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/tournaments', [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => json_encode(['name' => 'Tournament'])
        ]);
        $this->assertResponseStatusCodeSame(200);
        $tournamentId = $client->getResponse()->toArray()["id"];

        $client->request('POST', "/api/tournaments/$tournamentId/participants", [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => json_encode([
                "name"  => "Novak Djokovic"
            ])
        ]);

        $this->assertResponseStatusCodeSame(400, "A Participant must have elo");
    }

    public function testParticipantCreationWithNoNameShouldNotWork(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/tournaments', [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => json_encode(['name' => 'Tournament'])
        ]);
        $this->assertResponseStatusCodeSame(200);
        $tournamentId = $client->getResponse()->toArray()["id"];

        $client->request('POST', "/api/tournaments/$tournamentId/participants", [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => json_encode([
                "elo"  => 2500
            ])
        ]);

        $this->assertResponseStatusCodeSame(400, "A Participant must have name");
    }

    public function testParticipantCreationParticipantExistAlready(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/tournaments', [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => json_encode(['name' => 'Tournament'])
        ]);
        $this->assertResponseStatusCodeSame(200);
        $tournamentId = $client->getResponse()->toArray()["id"];

        $client->request('POST', "/api/tournaments/$tournamentId/participants", [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => json_encode([
                "name"  => "Novak Djokovic",
                "elo"  => 2500
            ])
        ]);

        $this->assertResponseStatusCodeSame(200);

        $client->request('POST', "/api/tournaments/$tournamentId/participants", [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => json_encode([
                "name"  => "Novak Djokovic",
                "elo"   => 2500
            ])
        ]);

        $this->assertResponseStatusCodeSame(400, "A participant already exist");
    }
}
