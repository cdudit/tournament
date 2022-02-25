<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ParticipantService
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
}
