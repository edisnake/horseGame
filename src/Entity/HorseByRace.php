<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HorseByRaceRepository")
 */
class HorseByRace
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Horse")
     * @ORM\JoinColumn(nullable=false)
     */
    private $horse;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Race")
     * @ORM\JoinColumn(nullable=false)
     */
    private $race;

    /**
     * @ORM\Column(type="float")
     */
    private $currentDistance;

    /**
     * @ORM\Column(type="float")
     */
    private $spentTime;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHorse(): ?Horse
    {
        return $this->horse;
    }

    public function setHorse(Horse $horse): self
    {
        $this->horse = $horse;

        return $this;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(Race $race): self
    {
        $this->race = $race;

        return $this;
    }

    public function getCurrentDistance(): ?float
    {
        return $this->currentDistance;
    }

    public function setCurrentDistance(float $currentDistance): self
    {
        $this->currentDistance = $currentDistance;

        return $this;
    }

    public function getSpentTime(): ?float
    {
        return $this->spentTime;
    }

    public function setSpentTime(float $spentTime): self
    {
        $this->spentTime = $spentTime;

        return $this;
    }
}
