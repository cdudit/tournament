<?php

namespace App\Services;

use App\Model\Participant;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ParticipantService
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getParticipant(string $id): ?Participant
    {
        return $this->session->get($id);
    }

    public function getParticipantByName(string $name): ?Participant
    {
        $participants = $this->session->all();

        foreach ($participants as $participant) {
            if ($participant->name == $name) {
                return $participant;
            }
        }

        return null;
    }

    public function saveParticipant(Participant $participant)
    {
        $this->session->set($participant->id, $participant);
        $this->session->save();
    }

    public function deleteParticipant(string $participantId)
    {
        $this->session->remove($participantId);
        $this->session->save();
    }
}
