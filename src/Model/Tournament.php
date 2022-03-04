<?php

namespace App\Model;

use Symfony\Component\Uid\Uuid;

class Tournament
{
    public string $id;
    public string $name;

    public function __construct($name)
    {
        $this->id = Uuid::v4();
        $this->name = $name;
    }
}
