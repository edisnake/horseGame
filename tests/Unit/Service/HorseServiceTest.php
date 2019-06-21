<?php

namespace App\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use App\Entity\Horse;
use App\Service\HorseService;
use App\Service\UtilService;
use Symfony\Component\Form\Extension\Validator\Constraints\Form as FormConstraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HorseServiceTest extends TestCase
{
    protected $horseService;
    protected $utilService;
    protected $validator;

    protected function setUp()
    {
        $this->utilService = $this->createMock(UtilService::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->horseService = new HorseService($this->utilService, $this->validator);
    }

    public function testCreateHorseValidateMethodFailed()
    {
        $this->utilService
            ->expects($this->once())
            ->method('getRandomHorseNickName')
            ->will($this->returnValue('Test Horse NickName'))
        ;

        $violation = new ConstraintViolationList([
            new ConstraintViolation(
                'Invalid speed',
                'Invalid speed detailed',
                [],
                null,
                'data',
                null,
                null,
                null,
                new FormConstraint()
            )
        ]);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($violation))
        ;

        $this->expectException(\InvalidArgumentException::class);
        $this->horseService->createHorse(0.2, 0.4, 0.6);
    }

    public function testCreateHorseSuccessful()
    {
        $expectedNickname = 'Great Hamburg';

        $this->utilService
            ->expects($this->once())
            ->method('getRandomHorseNickName')
            ->will($this->returnValue($expectedNickname))
        ;

        $violation = new ConstraintViolationList([]);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($violation))
        ;

        // Expected data
        $expectedSpeed = 3.3;
        $expectedStrength = 5.4;
        $expectedEndurance = 8.1;
        $expectedBestSpeed = $expectedSpeed + HorseService::BASE_SPEED;
        $expectedAutonomy = $expectedEndurance * HorseService::ENDURANCE_FACTOR;
        $expectedSlowdown = HorseService::JOCKEY_SLOWDOWN - ($expectedStrength * HorseService::STRENGTH_FACTOR * HorseService::JOCKEY_SLOWDOWN);

        $horse = $this->horseService->createHorse($expectedSpeed, $expectedStrength, $expectedEndurance);

        // Assertions
        $this->assertInstanceOf(Horse::class, $horse);
        $this->assertEquals($expectedSpeed, $horse->getSpeed());
        $this->assertEquals($expectedStrength, $horse->getStrength());
        $this->assertEquals($expectedEndurance, $horse->getEndurance());
        $this->assertEquals($expectedNickname, $horse->getNickName());
        $this->assertEquals($expectedBestSpeed, $horse->getBestSpeed());
        $this->assertEquals($expectedAutonomy, $horse->getAutonomy());
        $this->assertEquals($expectedSlowdown, $horse->getSlowdown());
    }

    public function testGetHorseRealSpeedWithLessDistanceThanAutonomy()
    {
        $horse = new Horse();
        $horse->setAutonomy(550);
        $horse->setBestSpeed(12);
        $horse->setSlowdown(4.33);
        $distance = 499;
        $expectedSpeed = $horse->getBestSpeed();
        $realSpeed = $this->horseService->getHorseRealSpeed($horse, $distance);

        $this->assertEquals($expectedSpeed, $realSpeed);
    }


    public function testGetHorseRealSpeedWithGreaterDistanceThanAutonomy()
    {
        $horse = new Horse();
        $horse->setAutonomy(550);
        $horse->setBestSpeed(12);
        $horse->setSlowdown(4.33);
        $distance = 788;
        $expectedSpeed = $horse->getBestSpeed() - $horse->getSlowdown();
        $realSpeed = $this->horseService->getHorseRealSpeed($horse, $distance);

        $this->assertEquals($expectedSpeed, $realSpeed);
    }

    protected function tearDown()
    {
        unset($this->utilService);
        unset($this->validator);
        unset($this->horseService);
    }
}
