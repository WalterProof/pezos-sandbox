<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Tags;

use PezosSandbox\Domain\Model\Tag\CouldNotFindTag;
use PezosSandbox\Domain\Model\Tag\TagId;

interface Tags
{
    /**
     * @throws CouldNotFindTag
     */
    public function getOneById(TagId $tagId): Tag;

    /**
     * @return array<Tag>
     */
    public function listTags(): array;
}
