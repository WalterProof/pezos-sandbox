<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Domain\Model\Tag\TagId;

final class RemoveTag
{
    private string $tagId;

    /**
     * @return void
     *
     * */
    public function __construct(string $tagId)
    {
        $this->tagId = $tagId;
    }

    public function tagId(): TagId
    {
        return TagId::fromString($this->tagId);
    }
}
