<?php

namespace App\Controller;

use App\Service\RaceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RaceMainController extends AbstractController
{
    /**
     * @var RaceService
     */
    private $raceService;

    /**
     * RaceMainController constructor.
     * @param RaceService $raceService
     */
    public function __construct(RaceService $raceService)
    {
        $this->raceService = $raceService;
    }

    /**
     * @Route("/", name="raceMain")
     */
    public function index()
    {
        return $this->render('raceMain/index.html.twig', [
            'activeRaces' => $this->raceService->getActiveRacesWithHorses(),
            'completedRaces' => $this->raceService->getLastCompletedRacesWithHorses(),
            'bestEverHorse' => $this->raceService->getBestEverHorse()
        ]);
    }

    /**
     * @Route("/create", name="createRace")
     */
    public function createRace()
    {
        try {
            $this->raceService->createRace();
        } catch (\Exception $e) {
            $this->addFlash(
                'warning',
                "Something went wrong creating the race. " . $e->getMessage()
            );
        }

        return $this->redirectToRoute('raceMain');
    }

    /**
     * @Route("/progress", name="progressRaces")
     */
    public function progressRaces()
    {
        try {
            $this->raceService->progressRaces();
        } catch (\Exception $e) {
            $this->addFlash(
                'warning',
                "Something went wrong doing progress on the races. " . $e->getMessage()
            );
        }

        return $this->redirectToRoute('raceMain');
    }
}
