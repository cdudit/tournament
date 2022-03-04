<?php

namespace App\Controller;

use App\Model\Participant;
use App\Model\Tournament;
use App\Services\ParticipantService;
use App\Services\TournamentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends AbstractController
{
    private ParticipantService $participantService;
    private TournamentService $tournamentService;

    public function __construct(ParticipantService $participantService, TournamentService $tournamentService)
    {
        $this->participantService = $participantService;
        $this->tournamentService = $tournamentService;
    }

    /**
     * @Route("/api/tournaments/{tournamentId}/participants", name="add_participant", methods={"POST"})
     */
    public function addParticipant(Request $request, string $tournamentId): Response
    {
        $parametersAsArray = json_decode($request->getContent(), true);

        if ($this->tournamentService->getTournament($tournamentId) == null) {
            return $this->json("Tournament not found", Response::HTTP_NOT_FOUND);
        }

        if (!isset($parametersAsArray["name"]) || !isset($parametersAsArray["elo"])) {
            return $this->json('Participant should have a name or an elo', Response::HTTP_BAD_REQUEST);
        }

        if ($this->participantService->getParticipantByName($parametersAsArray["name"]) != null) {
            return $this->json('Participant should not have the same name', Response::HTTP_BAD_REQUEST);
        }

        $participant = new Participant($parametersAsArray["name"], $parametersAsArray["elo"], $tournamentId);
        $this->participantService->saveParticipant($participant);
        return $this->json(['id' => $participant->id]);
    }
}
