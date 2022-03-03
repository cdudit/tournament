<?php

namespace App\Model;

use Symfony\Component\Uid\Uuid;

class Participant
{
    public string $id;
    public string $name;
    public int $elo;
    public string $tournamentId;

    public function __construct(string $name, int $elo, string $tournamentId)
    {
        $this->id = Uuid::v4();
        $this->tournamentId = $tournamentId;
        $this->name = $name;
        $this->elo = $elo;
    }
}
