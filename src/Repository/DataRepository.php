<?php

namespace App\Repository;

use App\Entity\Data;
use App\Entity\Type;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Data|null find($id, $lockMode = null, $lockVersion = null)
 * @method Data|null findOneBy(array $criteria, array $orderBy = null)
 * @method Data[]    findAll()
 * @method Data[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Data::class);
    }

    public function arrayFindAll()
    {
        return $this->createQueryBuilder('d')
            ->join(Type::class, 't', Join::WITH, 'd.type = t.id')
            ->select(['t.name as type, d.value, d.date, d.unit'])
            ->getQuery()
            ->getArrayResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Data
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
