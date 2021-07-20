<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Category;

interface CategoryRepository
{
    public function save(Category $category): void;

    public function getById(CategoryId $categoryId): Category;

    public function nextIdentity(): CategoryId;
}
