<?php

namespace App\Service;

use App\Entity\HorseByRace;
use App\Entity\Race;
use App\Repository\RaceRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class RaceService
 * @package App\Service
 */
class RaceService
{
    const ALLOWED_RACES = 3;
    const MAX_DISTANCE = 1500.0;
    const LAST_COMPLETED_RACES = 5;
    const TOP_COMPLETED_AMOUNT = 3;
    const PROGRESS_SECONDS = 10.0;

    /**
     * @var HorseByRaceService
     */
    private $horseByRaceService;

    /**
     * @var RaceRepository
     */
    private $raceRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityMgr;

    /**
     * RaceService constructor.
     * @param HorseByRaceService $horseByRaceService
     * @param RaceRepository $raceRepository
     * @param EntityManagerInterface $entityMgr
     */
    public function __construct(HorseByRaceService $horseByRaceService, RaceRepository $raceRepository, EntityManagerInterface $entityMgr)
    {
        $this->horseByRaceService = $horseByRaceService;
        $this->raceRepository = $raceRepository;
        $this->entityMgr = $entityMgr;
    }

    /**
     * @throws \Exception
     */
    public function createRace(): void
    {
        if (count($this->raceRepository->getActiveRaces()) > self::ALLOWED_RACES) {
            throw new \InvalidArgumentException("Only " . self::ALLOWED_RACES . " races are allowed at the same time.");
        }

        $race = new Race();
        $race->setActive(1);
        $race->setCreatedAt(new \DateTime());
        $race->setMaxDistance(self::MAX_DISTANCE);
        $race->setDuration(0);

        $this->entityMgr->beginTransaction();

        try {
            $this->entityMgr->persist($race);
            $horses = $this->horseByRaceService->generateHorsesForRace();

            foreach ($horses as $horse) {
                $this->entityMgr->persist($horse);
                $this->entityMgr->persist($this->horseByRaceService->createHorseByRace($race, $horse));
            }

            $this->entityMgr->flush();
            $this->entityMgr->commit();
        } catch (\Throwable $e) {
            $this->entityMgr->rollback();
            throw new \InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getActiveRacesWithHorses(): array
    {
        $activeRaces = $this->raceRepository->getActiveRaces();
        $result = [];

        foreach ($activeRaces as $activeRace) {
            $result[] = [
                'race' => $activeRace,
                'horses' => $this->horseByRaceService->getHorsesByRace($activeRace->getId())
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getLastCompletedRacesWithHorses(): array
    {
        $completedRaces = $this->raceRepository->getLastCompletedRaces(self::LAST_COMPLETED_RACES);
        $result = [];

        foreach ($completedRaces as $completedRace) {
            $horses = array_slice(
                $this->horseByRaceService->getHorsesByRace($completedRace->getId()),
                0,
                self::TOP_COMPLETED_AMOUNT
            );

            $result[] = [
                'race' => $completedRace,
                'horses' => $horses
            ];
        }

        return $result;
    }

    /**
     * @return HorseByRace|null
     */
    public function getBestEverHorse(): ?HorseByRace
    {
        return $this->horseByRaceService->getBestEverHorse();
    }


    /**
     * @throws \Exception
     */
    public function progressRaces(): void
    {
        $activeRaces = $this->raceRepository->getActiveRaces();

        if (count($activeRaces) === 0) {
            throw new \InvalidArgumentException("There are no active races at this moment.");
        }

        $this->entityMgr->beginTransaction();

        try {
            foreach ($activeRaces as $activeRace) {
                $horses = $this->horseByRaceService->progressHorsesByRace($activeRace, self::PROGRESS_SECONDS);

                foreach ($horses['horsesByRace'] as $horseByRace) {
                    $this->entityMgr->persist($horseByRace);
                }

                // If all horses have completed the race
                if ($horses['completedRace']) {
                    $activeRace->setActive(0);
                    $this->entityMgr->persist($activeRace);
                }
            }

            $this->entityMgr->flush();
            $this->entityMgr->commit();
        } catch (\Throwable $e) {
            $this->entityMgr->rollback();
            throw new \InvalidArgumentException($e->getMessage());
        }
    }
}
