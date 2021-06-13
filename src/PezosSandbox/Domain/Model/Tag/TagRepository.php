<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Tag;

interface TagRepository
{
    public function save(Tag $tag): void;

    public function getById(TagId $tagId): Tag;

    public function nextIdentity(): TagId;
}
