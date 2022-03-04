<?php

namespace App\Tests\Acceptance;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Model\Participant;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class TournamentTest extends ApiTestCase
{
    public function testTournamentCreation(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/tournaments', [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => json_encode(['name' => 'Tournament'])
        ]);

        $this->assertResponseIsSuccessful();
        $response = $client->getResponse()->toArray();

        $this->assertIsString($response["id"]);
    }

    public function testTournamentShouldHaveName(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/tournaments', [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => json_encode([])
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testTournamentShouldHaveUniqueName(): void
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

        $client->request('POST', '/api/tournaments', [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => json_encode(['name' => 'Tournament'])
        ]);
        $this->assertResponseStatusCodeSame(409, "A Tournament has already this name");
    }

    public function testTournamentCreationShouldEnableToRetrieveAfter(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/tournaments', [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => json_encode(['name' => 'Tournament'])
        ]);

        $this->assertResponseIsSuccessful();
        $response = $client->getResponse()->toArray();
        $tournamentId = $response["id"];

        $this->assertIsString($tournamentId);

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
        $this->assertResponseStatusCodeSame(200);
        $client->request('GET', "/api/tournaments/$tournamentId");

        $response = json_decode(json_decode($client->getResponse()->getContent()));
        $participantId = $response->id;

        $this->assertResponseIsSuccessful();
        $this->assertEquals("Tournament", $response->name);

        $this->assertArrayHasKey(0, $response->participants);
        $this->assertJsonEquals([
            "id"    => $response->id,
            "name"  => "Tournament",
            "participants"   => [
                json_encode([
                    "id"    => $participantId,
                    "name"  => "Novak Djokovic",
                    "elo"   => 2500
                ])
            ]
        ]);
    }

    public function testShouldReturnEmptyIfTournamentDoesNotExist(): void
    {
        static::createClient()->request('GET', '/api/tournaments/123');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTournamentGetParticipants(): void
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
        $this->assertResponseStatusCodeSame(200);
        $participantId = $client->getResponse()->toArray()["id"];

        $client->request('GET', "/api/tournaments/$tournamentId/participants");
        $this->assertResponseStatusCodeSame(200);
        $responseBody = $client->getResponse()->toArray();
        $this->assertArrayHasKey(0, $responseBody);
        $this->assertArrayHasKey("id", $responseBody[0]);
        $this->assertArrayHasKey("name", $responseBody[0]);
        $this->assertArrayHasKey("elo", $responseBody[0]);
        $this->assertJsonEquals([
            0 => [
                "id"    => $participantId,
                "name"  => "Novak Djokovic",
                "elo"   => 2500,
            ]
        ]);
    }

    public function testTournamentGetParticipantsNotExists(): void
    {
        $tournamentId = Uuid::v4();
        static::createClient()->request('GET', "/api/tournaments/$tournamentId/participants");
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
