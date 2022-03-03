<?php

namespace App\Model;

use Symfony\Component\Uid\Uuid;

class Participant
{
    public string $id;
    public string $name;
    public int $elo;
    public string $tounamentId;

    public function __construct(string $name, int $elo, string $tounamentId)
    {
        $this->id = Uuid::v4();
        $this->tounamentId = $tounamentId;
        $this->name = $name;
        $this->elo = $elo;
    }
}
