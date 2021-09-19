<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PriceHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PriceHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PriceHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method PriceHistory[]    findAll()
 * @method PriceHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PriceHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceHistory::class);
    }

    /**
     * @param mixed $value
     *
     * @return string[] Returns an array of token identifiers
     */
    public function findAllTokens()
    {
        $res = $this->createQueryBuilder('p')
            ->select('p.token')
            ->distinct()
            ->getQuery()
            ->getScalarResult();

        return array_column($res, 'token');
    }

    /**
     * @return PriceHistory[]
     */
    public function fromDate(string $token, \DateTimeInterface $fromDate): array
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.token = :token')
            ->andWhere('p.timestamp > :fromDate')
            ->setParameters([
                'token'    => $token,
                'fromDate' => $fromDate->format('Y-m-d H:i:s'),
            ])
            ->orderBy('p.timestamp', 'asc')
            ->getQuery()
            ->getResult();
    }
}
