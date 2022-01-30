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
     * @return string[] Returns an array of token identifiers
     */
    public function findAllTokens(): array
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
    public function pricesFromDate(
        string $token,
        ?string $datePart,
        ?\DateTimeInterface $fromDate = null
    ): array {
        $conn = $this->getEntityManager()->getConnection();
        $sql  =
            'SELECT '.
            (null !== $datePart
                ? sprintf(
                    "DATE_TRUNC('%s', timestamp) as timestamp, avg(price) AS price",
                    $datePart
                )
                : 'timestamp, price').
            ' FROM price_history WHERE token = :token'.
            (null !== $fromDate ? ' AND timestamp > :fromDate' : '');

        if (null !== $datePart) {
            $sql .= ' GROUP BY timestamp';
        }

        $sql .= ' ORDER BY timestamp ASC';

        $parameters = [
            'token' => $token,
        ];

        if (null !== $fromDate) {
            $parameters['fromDate'] = $fromDate->format('Y-m-d H:i:s');
        }

        return $conn->executeQuery($sql, $parameters)->fetchAllAssociative();
    }

    /**
     * @return PriceHistory[]
     */
    public function poolsFromDate(
        string $token,
        ?\DateTimeInterface $fromDate = null
    ): array {
        $conn = $this->getEntityManager()->getConnection();
        $sql  =
            'SELECT timestamp, price, tezpool, tokenpool FROM price_history WHERE token = :token'.
            (null !== $fromDate ? ' AND timestamp > :fromDate' : '');

        $sql .= ' ORDER BY timestamp ASC';

        $parameters = [
            'token' => $token,
        ];

        if (null !== $fromDate) {
            $parameters['fromDate'] = $fromDate->format('Y-m-d H:i:s');
        }

        return $conn->executeQuery($sql, $parameters)->fetchAllAssociative();
    }
}
