<?php

namespace App\Controller;

use App\Services\TournamentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends AbstractController
{
    private TournamentService $service;
    public function __construct(TournamentService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/api/tournaments/{tournamentId}/participants", name="add_participant", methods={"POST"})
     */
    public function addParticipant(string $tournamentId, Request $request): Response
    {
        return $this->json("", 200);
    }
}
