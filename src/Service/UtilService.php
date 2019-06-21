<?php

namespace App\Service;


/**
 * Class UtilService
 * @package App\Service
 */
class UtilService
{
    /**
     * @var array
     */
    private $nickNameList;

    /**
     * @var integer
     */
    private $nickNameListSize;

    /**
     * UtilService constructor.
     */
    public function __construct()
    {
        $this->nickNameList = explode(',', $_ENV['HORSES_NICKNAMES']);
        $this->nickNameListSize = sizeof($this->nickNameList) - 1;
    }

    /**
     * Generates a random horse nickname
     * @return string
     */
    public function getRandomHorseNickName(): string
    {
        $randIndex = mt_rand(0, $this->nickNameListSize);

        return $this->nickNameList[$randIndex];
    }

    /**
     * Generates a decimal random horse stat
     *
     * @param int $min
     * @param int $max
     * @return float
     */
    public function getRandomHorseStat(int $min, int $max): float
    {
        $factor = 10;
        return mt_rand($min * $factor, $max * $factor) / $factor;
    }

    /**
     * @param array $nickNameList
     */
    public function setNickNameList(array $nickNameList)
    {
        $this->nickNameList = $nickNameList;
    }

    /**
     * @param int $nickNameListSize
     */
    public function setNickNameListSize(int $nickNameListSize)
    {
        $this->nickNameListSize = $nickNameListSize;
    }
}
