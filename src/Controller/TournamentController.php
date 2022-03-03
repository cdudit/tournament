<?php

namespace App\Controller;

use App\Model\Tournament;
use App\Services\ParticipantService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\TournamentService;
use Symfony\Component\Uid\Uuid;

class TournamentController extends AbstractController
{
    private TournamentService $tournamentService;
    private ParticipantService $participantService;

    public function __construct(TournamentService $tournamentService, ParticipantService $participantService)
    {
        $this->tournamentService = $tournamentService;
        $this->participantService = $participantService;
    }

    /**
     * @Route("/api/tournaments", name="create_tournament", methods={"POST"})
     */
    public function addTournament(Request $request): Response
    {
        $parametersAsArray = json_decode($request->getContent(), true);
        $uuid = Uuid::v4();

        if (!isset($parametersAsArray["name"])) {
            return $this->json('Tournament should have a name', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $name = $parametersAsArray["name"];
        if ($this->tournamentService->getTournamentByName($name) === null) {
            $tournament = new Tournament($uuid, $name);
            $this->tournamentService->saveTournament($tournament);
            return $this->json(['id' => $uuid]);
        } else {
            return $this->json('A tournament already exist', Response::HTTP_CONFLICT);
        }
    }

    /**
     * @Route("/api/tournaments/{id}", name="get_tournament", methods={"GET"})
     */
    public function getTournament(string $id): Response
    {
        $tournament = $this->tournamentService->getTournament($id);
        if (null == $tournament) {
            throw $this->createNotFoundException();
        }
        return $this->json($tournament);
    }

    /**
     * @Route("/api/tournaments/{id}/participants", name="get_participants_by_tournament", methods={"GET"})
     */
    public function getParticipantsByTournament(string $id): Response
    {
        $tournament = $this->tournamentService->getTournament($id);
        if (null == $tournament) {
            throw $this->createNotFoundException();
        }

        return $this->json($this->tournamentService->getParticipants($id));
    }

    /**
     * @Route("/api/tournaments/{tournamentId}/participants/{participantId}", name="delete_participants_of_tournament", methods={"DELETE"})
     */
    public function deleteParticipantOfTournament(string $tournamentId, string $participantId): Response
    {
        $tournament = $this->tournamentService->getTournament($tournamentId);
        if (null == $tournament) {
            throw $this->createNotFoundException();
        }

        $participants = $this->tournamentService->getParticipants($tournamentId);

        foreach ($participants as $participant) {
            if ($participant["id"] == $participantId) {
                $this->participantService->deleteParticipant($participantId);
                return $this->json([], Response::HTTP_NO_CONTENT);
            }
        }
        return $this->json("Participant not found", Response::HTTP_NOT_FOUND);
    }
}
