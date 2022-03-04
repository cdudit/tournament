<?php

namespace App\Tests\Acceptance;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class TournamentTest extends ApiTestCase
{
    protected $client;
    protected $tournamentId;
    protected $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->request('POST', '/api/tournaments', [
            'headers' => $this->headers,
            'body' => json_encode(['name' => 'Tournament'])
        ]);
        $this->tournamentId = $this->client->getResponse()->toArray()["id"];
    }

    public function testTournamentCreation(): void
    {
        $response = $this->client->getResponse()->toArray();
        $this->assertIsString($response["id"]);
    }

    public function testTournamentShouldHaveName(): void
    {
        $this->client->request('POST', '/api/tournaments', [
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
        $this->client->request('POST', '/api/tournaments', [
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
        $response = $this->client->getResponse()->toArray();

        $this->client->request('POST', "/api/tournaments/$this->tournamentId/participants", [
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
        $this->client->request('GET', "/api/tournaments/$this->tournamentId");

        $response = json_decode(json_decode($this->client->getResponse()->getContent()));
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
        $id = Uuid::v4();
        $this->client->request('GET', "/api/tournaments/$id");
        $this->assertResponseStatusCodeSame(404);
    }

    public function testTournamentGetParticipants(): void
    {
        $this->client->request('POST', "/api/tournaments/$this->tournamentId/participants", [
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
        $participantId = $this->client->getResponse()->toArray()["id"];

        $this->client->request('GET', "/api/tournaments/$this->tournamentId/participants");
        $this->assertResponseStatusCodeSame(200);
        $responseBody = $this->client->getResponse()->toArray();
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
