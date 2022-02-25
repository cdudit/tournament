<?php

namespace App\Model;

class Participant
{
    public string $name;
    public string $elo;

    public function __construct($name, $elo)
    {
        $this->name = $name;
        $this->elo = $elo;
    }
}
