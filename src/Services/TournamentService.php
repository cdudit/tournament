<?php

namespace App\Services;

use App\Model\Participant;
use App\Model\Tournament;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TournamentService
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getTournament(string $id): ?Tournament
    {
        return $this->session->get($id);
    }

    public function saveTournament(Tournament $tournament)
    {
        $this->session->set($tournament->id, $tournament);
        $this->session->save();
    }

    public function getTournamentByName(string $name): ?Tournament
    {
        $tournaments = $this->session->all();

        foreach ($tournaments as $tournament) {
            if ($tournament->name == $name) {
                return $tournament;
            }
        }
        return null;
    }

    public function getParticipants(string $tournamentId)
    {
        $result = array();
        foreach ($this->session->all() as $sessionItem) {
            if (get_class($sessionItem) == Participant::class && $sessionItem->tournamentId == $tournamentId) {
                $participantToAdd = array("id" => $sessionItem->id, "name" => $sessionItem->name, "elo" => $sessionItem->elo);
                array_push($result, $participantToAdd);
            }
        }

        return $result;
    }
}
