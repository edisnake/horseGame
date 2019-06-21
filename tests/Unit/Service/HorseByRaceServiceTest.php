<?php

namespace App\Tests\Unit\Service;

use App\Entity\Horse;
use App\Entity\HorseByRace;
use App\Entity\Race;
use App\Service\HorseByRaceService;
use App\Repository\HorseByRaceRepository;
use App\Service\HorseService;
use App\Service\UtilService;
use PHPUnit\Framework\TestCase;

class HorseByRaceServiceTest extends TestCase
{
    protected $horseByRaceService;
    protected $horseService;
    protected $race;
    protected $horse;
    protected $utilService;
    protected $horseByRaceRepository;

    protected function setUp()
    {
        $this->race = $this->createMock(Race::class);
        $this->horse = $this->createMock(Horse::class);
        $this->horseService = $this->getMockBuilder(HorseService::class)
                                ->disableOriginalConstructor()
                                ->disableOriginalClone()
                                ->disableArgumentCloning()
                                ->disallowMockingUnknownTypes()
                                ->setMethods(['createHorse'])
                                ->getMock();

        $this->utilService = $this->createMock(UtilService::class);
        $this->horseByRaceRepository = $this->createMock(HorseByRaceRepository::class);
        $this->horseByRaceService = new HorseByRaceService($this->horseService, $this->utilService, $this->horseByRaceRepository);
    }

    public function testCreateHorseByRace()
    {
        $horseByRace = $this->horseByRaceService->createHorseByRace($this->race, $this->horse);
        $expectedDistance = 0;
        $expectedSpentTime = 0;

        // Assertions
        $this->assertInstanceOf(HorseByRace::class, $horseByRace);
        $this->assertEquals($this->race, $horseByRace->getRace());
        $this->assertEquals($this->horse, $horseByRace->getHorse());
        $this->assertEquals($expectedDistance, $horseByRace->getCurrentDistance());
        $this->assertEquals($expectedSpentTime, $horseByRace->getSpentTime());
    }

    public function testGenerateHorsesForRace()
    {
        $this->horseService
            ->expects($this->exactly(HorseByRaceService::MAX_HORSES_BY_RACE))
            ->method('createHorse')
        ;

        $horses = $this->horseByRaceService->generateHorsesForRace();

        // Assertions
        $this->assertInternalType('array', $horses);
        $this->assertEquals(HorseByRaceService::MAX_HORSES_BY_RACE, count($horses));
        $this->assertContainsOnlyInstancesOf(Horse::class, $horses);
    }

    public function testGetBestEverHorseReturnsData()
    {
        $this->horseByRaceRepository
            ->expects($this->once())
            ->method('findBestEverTime')
            ->will($this->returnValue([
                new HorseByRace(),
                new HorseByRace(),
            ]))
        ;

        $horseByRace = $this->horseByRaceService->getBestEverHorse();

        // Assertions
        $this->assertInstanceOf(HorseByRace::class, $horseByRace);
    }

    public function testGetBestEverHorseReturnsNoData()
    {
        $this->horseByRaceRepository
            ->expects($this->once())
            ->method('findBestEverTime')
            ->will($this->returnValue([]))
        ;

        $horseByRace = $this->horseByRaceService->getBestEverHorse();

        // Assertions
        $this->assertNull($horseByRace);
    }

    public function testGetHorsesByRaceReturnsData()
    {
        $this->horseByRaceRepository
            ->expects($this->once())
            ->method('findAllHorsesByRace')
            ->will($this->returnValue([
                new HorseByRace(),
                new HorseByRace(),
                new HorseByRace(),
            ]))
        ;

        $horsesByRace = $this->horseByRaceService->getHorsesByRace(10);

        // Assertions
        $this->assertInternalType('array', $horsesByRace);
        $this->assertEquals(3, count($horsesByRace));
        $this->assertContainsOnlyInstancesOf(HorseByRace::class, $horsesByRace);
    }

    public function testGetHorsesByRaceReturnsNoData()
    {
        $this->horseByRaceRepository
            ->expects($this->once())
            ->method('findAllHorsesByRace')
            ->will($this->returnValue([]))
        ;

        $horsesByRace = $this->horseByRaceService->getHorsesByRace(5);

        // Assertions
        $this->assertEmpty($horsesByRace);
    }

    /**
     * @group progress
     */
    public function testProgressHorsesByRaceNoCompleted()
    {
        $this->race
            ->method('getId')
            ->will($this->returnValue(52));

        $this->race
            ->method('getMaxDistance')
            ->will($this->returnValue(1500));

        $this->horse
            ->method('getAutonomy')
            ->will($this->returnValue(999));

        $this->horse
            ->method('getBestSpeed')
            ->will($this->returnValue(10.4));

        $this->horse
            ->method('getSlowdown')
            ->will($this->returnValue(3.4));


        $horseByRace = new HorseByRace();
        $horseByRace->setCurrentDistance(600);
        $horseByRace->setSpentTime(60);
        $horseByRace->setHorse($this->horse);
        $horseByRace->setRace($this->race);


        $this->horseByRaceRepository
            ->expects($this->once())
            ->method('findAllHorsesByRace')
            ->will($this->returnValue([
                $horseByRace
            ]))
        ;

        $result = $this->horseByRaceService->progressHorsesByRace($this->race, 10.0);

        // Assertions
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('completedRace', $result);
        $this->assertArrayHasKey('horsesByRace', $result);
        $this->assertInternalType('bool', $result['completedRace']);
        $this->assertInternalType('array', $result['horsesByRace']);
        $this->assertFalse($result['completedRace']);

        $resultHorseByRace = $result['horsesByRace'][0] ?? null;
        $expectedDistance = 704.0;
        $expectedSpentTime = 70;

        $this->assertInstanceOf(HorseByRace::class, $resultHorseByRace);
        $this->assertEquals($expectedDistance, $resultHorseByRace->getCurrentDistance());
        $this->assertEquals($expectedSpentTime, $resultHorseByRace->getSpentTime());
    }

    /**
     * @group progress
     */
    public function testProgressHorsesByRaceCompleted()
    {
        $this->race
            ->method('getId')
            ->will($this->returnValue(52));

        $this->race
            ->method('getMaxDistance')
            ->will($this->returnValue(1500));

        $this->horse
            ->method('getAutonomy')
            ->will($this->returnValue(950));

        $this->horse
            ->method('getBestSpeed')
            ->will($this->returnValue(9.5));

        $this->horse
            ->method('getSlowdown')
            ->will($this->returnValue(2.86));


        $horseByRace = new HorseByRace();
        $horseByRace->setCurrentDistance(1440.2);
        $horseByRace->setSpentTime(173.83);
        $horseByRace->setHorse($this->horse);
        $horseByRace->setRace($this->race);


        $this->horseByRaceRepository
            ->expects($this->once())
            ->method('findAllHorsesByRace')
            ->will($this->returnValue([
                $horseByRace
            ]))
        ;

        $result = $this->horseByRaceService->progressHorsesByRace($this->race, 10.0);

        // Assertions
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('completedRace', $result);
        $this->assertArrayHasKey('horsesByRace', $result);
        $this->assertInternalType('bool', $result['completedRace']);
        $this->assertInternalType('array', $result['horsesByRace']);
        $this->assertFalse($result['completedRace']);

        $resultHorseByRace = $result['horsesByRace'][0] ?? null;
        $expectedDistance = 1500.0;
        $expectedSpentTime = 182.84;

        $this->assertInstanceOf(HorseByRace::class, $resultHorseByRace);
        $this->assertEquals($expectedDistance, $resultHorseByRace->getCurrentDistance());
        $this->assertEquals($expectedSpentTime, $resultHorseByRace->getSpentTime());
    }

    protected function tearDown()
    {
        unset($this->horseService);
        unset($this->horseByRaceService);
        unset($this->race);
        unset($this->horse);
        unset($this->utilService);
        unset($this->horseByRaceRepository);
    }
}
