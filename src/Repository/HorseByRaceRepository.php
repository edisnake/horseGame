<?php

namespace App\Repository;

use App\Entity\HorseByRace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HorseByRace|null find($id, $lockMode = null, $lockVersion = null)
 * @method HorseByRace|null findOneBy(array $criteria, array $orderBy = null)
 * @method HorseByRace[]    findAll()
 * @method HorseByRace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HorseByRaceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HorseByRace::class);
    }

    public function findAllActiveRacesWithHorse()
    {
        return $this->createQueryBuilder('hr')
            ->innerJoin('hr.race', 'r')
            ->addSelect('hr')
            ->andWhere('r.active = :activeValue')
            ->setParameter('activeValue', RaceRepository::ACTIVE)
            ->getQuery()
            ->getResult();
    }

    public function findAllHorsesByRace(int $raceId)
    {
        return $this->createQueryBuilder('hr')
            ->addSelect('hr')
            ->andWhere('hr.race = :param1')
            ->setParameter('param1', $raceId)
            ->orderBy('hr.currentDistance', 'DESC')
            ->addOrderBy('hr.spentTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBestEverTime()
    {
        return $this->createQueryBuilder('hr')
            ->innerJoin('hr.race', 'r')
            ->addSelect('hr')
            ->andWhere('r.active = :inactiveValue')
            ->setParameter('inactiveValue', RaceRepository::INACTIVE)
            ->orderBy('hr.spentTime', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
