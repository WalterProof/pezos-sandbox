<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TokenExchange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TokenExchange|null find($id, $lockMode = null, $lockVersion = null)
 * @method TokenExchange|null findOneBy(array $criteria, array $orderBy = null)
 * @method TokenExchange[]    findAll()
 * @method TokenExchange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenExchangeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenExchange::class);
    }

    // /**
    //  * @return TokenExchange[] Returns an array of TokenExchange objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TokenExchange
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
