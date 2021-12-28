<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Contract;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contract|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contract|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contract[]    findAll()
 * @method Contract[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contract::class);
    }

    public function findAllIdentifiers(): array
    {
        $res = $this->createQueryBuilder('c')
            ->select('c.identifier')
            ->getQuery()
            ->getScalarResult();

        return array_column($res, 'identifier');
    }

    public function findAllSelectable(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.identifier, c.symbol, c.name')
            ->getQuery()
            ->getScalarResult();
    }

    public function findAllWithPool()
    {
        //SELECT * FROM contract JOIN (SELECT ph1.* FROM price_history ph1 JOIN (SELECT token, MAX(timestamp) ts from price_history group by token) ph2 ON ph1.token = ph2.token A
        // ND ph1.timestamp = ph2.ts WHERE tezpool > 0) p ON contract.identifier = p.token;
    }

    // /**
    //  * @return Contract[] Returns an array of Contract objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Contract
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
