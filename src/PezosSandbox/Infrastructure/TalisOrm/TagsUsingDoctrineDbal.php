<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\TalisOrm;

use PezosSandbox\Application\Tags\Tag;
use PezosSandbox\Application\Tags\Tags;
use PezosSandbox\Domain\Model\Tag\CouldNotFindTag;
use PezosSandbox\Domain\Model\Tag\TagId;
use PezosSandbox\Infrastructure\Doctrine\Connection;
use PezosSandbox\Infrastructure\Doctrine\NoResult;
use PezosSandbox\Infrastructure\Mapping;

final class TagsUsingDoctrineDbal implements Tags
{
    use Mapping;

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getOneById(TagId $tagId): Tag
    {
        try {
            $qb = $this->connection
                ->createQueryBuilder()
                ->select('*')
                ->from('tags')
                ->andWhere('tag_id = :tagId')
                ->setParameter('tagId', $tagId->asString());

            $data = $this->connection->selectOne($qb);

            return $this->createTag($data);
        } catch (NoResult $exception) {
            throw CouldNotFindTag::withId($tagId);
        }
    }

    public function listTags(): array
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from('tags');
        $records = $this->connection->selectAll($qb);

        return array_map(
            fn (array $record): Tag => new Tag(
                TagId::fromString(self::asString($record, 'tag_id')),
                self::asString($record, 'label')
            ),
            $records
        );
    }

    /**
     * @param array<string,mixed> $data
     */
    private function createTag($data): Tag
    {
        return new Tag(
            TagId::fromString($data['tag_id']),
            self::asString($data, 'label')
        );
    }
}
