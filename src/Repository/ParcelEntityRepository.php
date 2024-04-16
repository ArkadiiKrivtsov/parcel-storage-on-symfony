<?php

namespace App\Repository;

use App\Entity\ParcelEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ParcelEntity>
 *
 * @method ParcelEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParcelEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParcelEntity[]    findAll()
 * @method ParcelEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParcelEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParcelEntity::class);
    }

//    /**
//     * @return ParcelEntity[] Returns an array of ParcelEntity objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ParcelEntity
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
