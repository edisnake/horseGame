<?php

namespace App\Tests\Unit\Service;

use App\Service\UtilService;
use PHPUnit\Framework\TestCase;


class UtilServiceTest extends TestCase
{
    protected $utilService;

    protected function setUp()
    {
        $this->utilService = new UtilService();
    }

    public function testGetRandomHorseNickNameDistribution()
    {
        $names = [];

        // Testing the random distribution
        for ($n = 0; $n < 100; $n++) {
            $nickName = $this->utilService->getRandomHorseNickName();
            $names[$nickName] = 1;
        }

        // Making sure at least 65 different names
        $this->assertGreaterThan(65, count($names));
    }

    public function testGetRandomHorseStatDistribution()
    {
        $stats = [];
        $min = 0;
        $max = 10;

        $this->assertInternalType('float', $this->utilService->getRandomHorseStat($min, $max));

        // Testing the random distribution
        for ($n = 0; $n < 50; $n++) {
            $floatNumber = $this->utilService->getRandomHorseStat($min, $max);
            $stats[strval($floatNumber)] = 1;
        }

        // Making sure at least 30 different numbers
        $this->assertGreaterThan(30, count($stats));
    }

    protected function tearDown()
    {
        unset($this->utilService);
    }
}
