<?php

namespace App\Repository;

use App\Entity\Station;
use App\Entity\StationUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StationUser>
 */
class StationUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StationUser::class);
    }


    public function findStationsByUserId(int $userId)
    {
        return $this->createQueryBuilder('su')
            ->select("su.id_station")
            ->andWhere('su.id_user  = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getArrayResult();
    }
    public function findStationNameById(int $stationId)
    {
        return $this->createQueryBuilder('su')
            ->select('s.name')
            ->innerJoin(Station::class, 's', 'WITH', 's.id = su.id_station OR s.station_id = su.id_station')
            ->where('su.id_station = :stationId')
            ->setParameter('stationId', $stationId)
            ->getQuery()
            ->getResult();
    }

    public function deleteStationByStationId(int $stationId)
    {
        return $this->createQueryBuilder('su')
            ->delete()
            ->andWhere("su.id_station = :stationId")
            ->setParameter("stationId", $stationId)
            ->getQuery()
            ->execute();
    }

    //    /**
    //     * @return StationUser[] Returns an array of StationUser objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?StationUser
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
