<?php

namespace App\Repository;

use App\Entity\Parking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Parking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parking[]    findAll()
 * @method Parking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParkingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Parking::class);
    }

    public function countByDate(\DateTime $date): int
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.date = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getSingleScalarResult();
    }
}