<?php

namespace App\Model;

class Tournament
{
    public string $id;
    public string $name;

    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
}