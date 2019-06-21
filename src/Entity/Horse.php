<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Entity(repositoryClass="App\Repository\HorseRepository")
*/
class Horse
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nickName;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *  min = 0.0,
     *  max = 10.0,
     *  minMessage = "The horse speed must be greather or equal than {{ limit }}m/s",
     *  maxMessage = "The horse speed must be less or equal than {{ limit }}m/s"
     * )
     */
    private $speed;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *  min = 0.0,
     *  max = 10.0,
     *  minMessage = "The horse strength must be greather or equal than {{ limit }}m/s",
     *  maxMessage = "The horse strength must be less or equal than {{ limit }}m/s"
     * )
     */
    private $strength;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *  min = 0.0,
     *  max = 10.0,
     *  minMessage = "The horse endurance must be greather or equal than {{ limit }}m/s",
     *  maxMessage = "The horse endurance must be less or equal than {{ limit }}m/s"
     * )
     */
    private $endurance;

    /**
     * @ORM\Column(type="float")
     */
    private $autonomy;

    /**
     * @ORM\Column(type="float")
     */
    private $slowdown;

    /**
     * @ORM\Column(type="float")
     */
    private $bestSpeed;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNickName(): ?string
    {
        return $this->nickName;
    }

    public function setNickName(string $nickName): self
    {
        $this->nickName = $nickName;

        return $this;
    }

    public function getSpeed(): ?float
    {
        return $this->speed;
    }

    public function setSpeed(float $speed): self
    {
        $this->speed = $speed;

        return $this;
    }

    public function getStrength(): ?float
    {
        return $this->strength;
    }

    public function setStrength(float $strength): self
    {
        $this->strength = $strength;

        return $this;
    }

    public function getEndurance(): ?float
    {
        return $this->endurance;
    }

    public function setEndurance(float $endurance): self
    {
        $this->endurance = $endurance;

        return $this;
    }

    public function getAutonomy(): ?float
    {
        return $this->autonomy;
    }

    public function setAutonomy(float $autonomy): self
    {
        $this->autonomy = $autonomy;

        return $this;
    }

    public function getSlowdown(): ?float
    {
        return $this->slowdown;
    }

    public function setSlowdown(float $slowdown): self
    {
        $this->slowdown = $slowdown;

        return $this;
    }

    public function getBestSpeed(): ?float
    {
        return $this->bestSpeed;
    }

    public function setBestSpeed(float $bestSpeed): self
    {
        $this->bestSpeed = $bestSpeed;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getNickName();
    }
}
