<?php

namespace App\Repository;

use App\Entity\Race;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Race|null find($id, $lockMode = null, $lockVersion = null)
 * @method Race|null findOneBy(array $criteria, array $orderBy = null)
 * @method Race[]    findAll()
 * @method Race[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RaceRepository extends ServiceEntityRepository
{
    const ACTIVE = 1;
    const INACTIVE = 0;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Race::class);
    }

    /**
     * @return array
     */
    public function getActiveRaces(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.active = :activeValue')
            ->setParameter('activeValue', self::ACTIVE)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param int $races
     * @return array
     */
    public function getLastCompletedRaces(int $races): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.active = :inactiveValue')
            ->setParameter('inactiveValue', self::INACTIVE)
            ->orderBy('r.completedAt', 'DESC')
            ->setMaxResults($races)
            ->getQuery()
            ->getResult()
            ;
    }
}
