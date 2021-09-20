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
    public function fromDate(
        string $token,
        ?\DateTimeInterface $fromDate = null
    ): array {
        $qb = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.token = :token')
            ->orderBy('p.timestamp', 'asc');

        $parameters = [
            'token' => $token,
        ];
        if (null !== $fromDate) {
            $qb = $qb->andWhere('p.timestamp > :fromDate');

            $parameters['fromDate'] = $fromDate->format('Y-m-d H:i:s');
        }

        $qb = $qb->setParameters($parameters);

        return $qb->getQuery()->getResult();
    }
}
