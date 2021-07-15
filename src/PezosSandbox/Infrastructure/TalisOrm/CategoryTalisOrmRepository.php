<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\TalisOrm;

use Assert\Assert;
use PezosSandbox\Domain\Model\Category\CouldNotFindCategory;
use PezosSandbox\Domain\Model\Category\Category;
use PezosSandbox\Domain\Model\Category\CategoryId;
use PezosSandbox\Domain\Model\Category\CategoryRepository;
use Ramsey\Uuid\Uuid;
use TalisOrm\AggregateNotFoundException;
use TalisOrm\AggregateRepository;

final class CategoryTalisOrmRepository implements CategoryRepository
{
    private AggregateRepository $aggregateRepository;

    public function __construct(AggregateRepository $aggregateRepository)
    {
        $this->aggregateRepository = $aggregateRepository;
    }

    public function save(Category $category): void
    {
        $this->aggregateRepository->save($category);
    }

    public function getById(CategoryId $categoryId): Category
    {
        try {
            $category = $this->aggregateRepository->getById(
                Category::class,
                $categoryId
            );
            Assert::that($category)->isInstanceOf(Category::class);
            /* @var Category $category */

            return $category;
        } catch (AggregateNotFoundException $exception) {
            throw CouldNotFindCategory::withId($categoryId);
        }
    }

    public function nextIdentity(): CategoryId
    {
        return CategoryId::fromString(Uuid::uuid4()->toString());
    }
}
