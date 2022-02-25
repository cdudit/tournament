<?php
namespace App\Services;

use App\Model\Tournament;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TournamentService {
    private SessionInterface $session;

    public function __construct(SessionInterface $session) {
        $this->session = $session;
    }

    public function getTournament(string $id) : ?Tournament {
        return $this->session->get($id);
    }

    public function saveTournament(Tournament $tournament) {
        $this->session->set($tournament->id, $tournament);
        $this->session->save();
    }
}