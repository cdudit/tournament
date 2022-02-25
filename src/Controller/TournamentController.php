<?php

namespace App\Controller;

use App\Model\Tournament;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\TournamentService;
use Symfony\Component\Uid\Uuid;

class TournamentController extends AbstractController
{
    private TournamentService $service;

    public function __construct(TournamentService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/api/tournaments", name="create_tournament", methods={"POST"})
     */
    public function addTournament(Request $request): Response
    {
        $parametersAsArray = json_decode($request->getContent(), true);
        $uuid = Uuid::v4();

        $tournament = new Tournament($uuid, $parametersAsArray["name"]);
        $this->service->saveTournament($tournament);

        return $this->json([
            'id' => $uuid,
        ]);
    }

    /**
     * @Route("/api/tournaments/{id}", name="get_tournament", methods={"GET"})
     */
    public function getTournament(string $id): Response
    {
        $tournament = $this->service->getTournament($id);
        if (null == $tournament) {
            throw $this->createNotFoundException();
        }
        return $this->json($tournament);
    }
}
