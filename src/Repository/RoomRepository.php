<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Room>
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    //    /**
    //     * @return Room[] Returns an array of Room objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Room
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

        /**
         * @return Room[] Returns an array of Room objects
         */
        public function roomPrivateAndFree(): array
        {
            return $this->createQueryBuilder('r')
                ->leftJoin('r.beds','b')
                ->andWhere('r.isPrivate = :val')
                ->andWhere('b.isOccupied = :isOccupied')
                ->andWhere('r.isPrivate = :val')
                ->setParameter('val', true)
                ->setParameter('isOccupied', false)
                ->getQuery()
                ->getResult()
                ->getCount()
            ;
        }
}
