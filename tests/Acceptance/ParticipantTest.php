<?php

namespace App\Tests\Acceptance;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\Uid\Uuid;

class ParticipantTest extends ApiTestCase
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

    public function testParticipantCreationShouldBeOK(): void
    {
        $this->client->request('POST', "/api/tournaments/$this->tournamentId/participants", [
            'headers' => $this->headers,
            'body' => json_encode([
                "name"          => "Novak Djokovic",
                "elo"           => 2500,
                "tounamentId"   => $this->tournamentId
            ])
        ]);

        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse()->toArray();
        $this->assertIsString($response["id"]);
    }

    public function testParticipantTournamentNotExists(): void
    {
        $id = Uuid::v4();
        $this->client->request('POST', "/api/tournaments/$id/participants", [
            'headers'   => $this->headers,
            'body'      => json_encode([
                "name"  => "Novak Djokovic",
                "elo"   => 2500
            ])
        ]);

        $this->assertResponseStatusCodeSame(404, "Tournament doesn't exists");
    }

    public function testParticipantCreationWithNoELOShouldNotWork(): void
    {
        $this->client->request('POST', "/api/tournaments/$this->tournamentId/participants", [
            'headers'   => $this->headers,
            'body'      => json_encode([
                "name"  => "Novak Djokovic"
            ])
        ]);

        $this->assertResponseStatusCodeSame(400, "A Participant must have elo");
    }

    public function testParticipantCreationWithNoNameShouldNotWork(): void
    {
        $this->client->request('POST', "/api/tournaments/$this->tournamentId/participants", [
            'headers'   => $this->headers,
            'body'      => json_encode([
                "elo"   => 2500
            ])
        ]);

        $this->assertResponseStatusCodeSame(400, "A Participant must have name");
    }

    public function testParticipantCreationParticipantExistAlready(): void
    {
        $this->client->request('POST', "/api/tournaments/$this->tournamentId/participants", [
            'headers'   => $this->headers,
            'body'      => json_encode([
                "name"  => "Novak Djokovic",
                "elo"   => 2500
            ])
        ]);

        $this->assertResponseStatusCodeSame(200);

        $this->client->request('POST', "/api/tournaments/$this->tournamentId/participants", [
            'headers'   => $this->headers,
            'body'      => json_encode([
                "name"  => "Novak Djokovic",
                "elo"   => 2500
            ])
        ]);

        $this->assertResponseStatusCodeSame(400, "A participant already exist");
    }

    public function testDeleteParticipantShouldWork(): void
    {
        $this->assertResponseStatusCodeSame(200);
        $this->client->request('POST', "/api/tournaments/$this->tournamentId/participants", [
            'headers'   => $this->headers,
            'body'      => json_encode([
                "name"  => "Novak Djokovic",
                "elo"   => 2500
            ])
        ]);
        $this->assertResponseStatusCodeSame(200);
        $participantId = $this->client->getResponse()->toArray()["id"];

        $this->client->request('DELETE', "/api/tournaments/$this->tournamentId/participants/$participantId");
        $this->assertResponseStatusCodeSame(204);

        $this->client->request('GET', "/api/tournaments/$this->tournamentId/participants");
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonEquals([]);
    }


    public function testDeleteParticipantNotExistShouldNotWork(): void
    {
        $participantId = Uuid::v4();
        $this->client->request('DELETE', "/api/tournaments/$this->tournamentId/participants/$participantId");
        $this->assertResponseStatusCodeSame(404);
    }
}
