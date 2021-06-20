<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\TalisOrm;

use Assert\Assert;
use PezosSandbox\Domain\Model\Tag\CouldNotFindTag;
use PezosSandbox\Domain\Model\Tag\Tag;
use PezosSandbox\Domain\Model\Tag\TagId;
use PezosSandbox\Domain\Model\Tag\TagRepository;
use Ramsey\Uuid\Uuid;
use TalisOrm\AggregateNotFoundException;
use TalisOrm\AggregateRepository;

final class TagTalisOrmRepository implements TagRepository
{
    private AggregateRepository $aggregateRepository;

    public function __construct(AggregateRepository $aggregateRepository)
    {
        $this->aggregateRepository = $aggregateRepository;
    }

    public function save(Tag $tag): void
    {
        $this->aggregateRepository->save($tag);
    }

    public function getById(TagId $tagId): Tag
    {
        try {
            $tag = $this->aggregateRepository->getById(Tag::class, $tagId);
            Assert::that($tag)->isInstanceOf(Tag::class);
            /* @var Tag $tag */

            return $tag;
        } catch (AggregateNotFoundException $exception) {
            throw CouldNotFindTag::withId($tagId);
        }
    }

    public function nextIdentity(): TagId
    {
        return TagId::fromString(Uuid::uuid4()->toString());
    }
}
