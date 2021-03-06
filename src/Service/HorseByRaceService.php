<?php

namespace App\Service;

use App\Entity\Horse;
use App\Entity\HorseByRace;
use App\Entity\Race;
use App\Repository\HorseByRaceRepository;

/**
 * Class HorseByRaceService
 * @package App\Service
 */
class HorseByRaceService
{
    const MAX_HORSES_BY_RACE = 8;
    const MIN_HORSE_STAT = 0;
    const MAX_HORSE_STAT = 10;

    /**
     * @var HorseService
     */
    private $horseService;

    /**
     * @var UtilService
     */
    private $utilService;

    /**
     * @var HorseByRaceRepository
     */
    private $horseByRaceRepository;

    /**
     * HorseByRaceService constructor.
     * @param HorseService $horseService
     * @param UtilService $utilService
     * @param HorseByRaceRepository $horseByRaceRepository
     */
    public function __construct(HorseService $horseService, UtilService $utilService, HorseByRaceRepository $horseByRaceRepository)
    {
        $this->horseService = $horseService;
        $this->utilService = $utilService;
        $this->horseByRaceRepository = $horseByRaceRepository;
    }

    /**
     * Generates a set of horses for a raceMain
     * @return array
     */
    public function generateHorsesForRace(): array
    {
        $horses = [];

        for ($h = 0; $h < self::MAX_HORSES_BY_RACE; ++$h) {
            $horses[] = $this->horseService->createHorse(
                $this->utilService->getRandomHorseStat(self::MIN_HORSE_STAT, self::MAX_HORSE_STAT),
                $this->utilService->getRandomHorseStat(self::MIN_HORSE_STAT, self::MAX_HORSE_STAT),
                $this->utilService->getRandomHorseStat(self::MIN_HORSE_STAT, self::MAX_HORSE_STAT)
            );
        }

        return $horses;
    }

    /**
     * Creates a HorseByRace entity object according to provided objects
     *
     * @param Race $race
     * @param Horse $horse
     * @return HorseByRace
     */
    public function createHorseByRace(Race $race, Horse $horse): HorseByRace
    {
        $horseByRace = new HorseByRace();
        $horseByRace->setRace($race);
        $horseByRace->setHorse($horse);
        $horseByRace->setCurrentDistance(0);
        $horseByRace->setSpentTime(0);

        return $horseByRace;
    }

    /**
     * @param int $raceId
     * @return array
     */
    public function getHorsesByRace(int $raceId): array
    {
        return $this->horseByRaceRepository->findAllHorsesByRace($raceId);
    }

    /**
     * @return HorseByRace|null
     */
    public function getBestEverHorse(): ?HorseByRace
    {
        return $this->horseByRaceRepository->findBestEverTime()[0] ?? null;
    }

    /**
     * @param Race $race
     * @param float $progressSeconds
     * @return array
     */
    public function progressHorsesByRace(Race $race, float $progressSeconds): array
    {
        $horsesByRace = $this->horseByRaceRepository->findAllHorsesByRace($race->getId());
        $completedHorsesCount = 0;

        foreach ($horsesByRace as $horseByRace) {
            $currentDistance = $horseByRace->getCurrentDistance();

            // If the horse has not completed the race
            if ($currentDistance < $race->getMaxDistance()) {
                $horseAutonomy = $horseByRace->getHorse()->getAutonomy();
                $horseRealSpeed = $this->horseService->getHorseRealSpeed($horseByRace->getHorse(), $currentDistance);
                $calculatedDistance = $currentDistance + $horseRealSpeed * $progressSeconds;
                $calculatedSeconds = $horseByRace->getSpentTime() + $progressSeconds;

                // Validation to change speed when the horse reach its autonomy distance
                if ($calculatedDistance > $horseAutonomy && $currentDistance < $horseAutonomy) {
                    // Calculating gap meters between autonomy distance and calculated distance
                    $gapMeters = $calculatedDistance - $horseAutonomy;
                    // Calculating gap seconds between autonomy distance and calculated distance
                    $gapSeconds = $gapMeters / $horseRealSpeed;
                    $horseRealSpeed = $this->horseService->getHorseRealSpeed($horseByRace->getHorse(), $calculatedDistance);
                    $calculatedDistance = $currentDistance + $gapSeconds * $horseRealSpeed;
                }

                // Fixing distance and time when calculated distance is greater than race's distance
                if ($calculatedDistance > $race->getMaxDistance()) {
                    $gapMeters = $race->getMaxDistance() - $currentDistance;
                    $calculatedDistance = $currentDistance + $gapMeters;
                    $gapSeconds = $gapMeters / $horseRealSpeed;
                    $calculatedSeconds = $horseByRace->getSpentTime() + $gapSeconds;
                }

                $horseByRace->setCurrentDistance(round($calculatedDistance, 2));
                $horseByRace->setSpentTime(round($calculatedSeconds, 2));
            } else {
                $completedHorsesCount++;
            }
        }

        return [
            'completedRace' => ($completedHorsesCount === self::MAX_HORSES_BY_RACE),
            'horsesByRace'  => $horsesByRace
        ] ;
    }
}
