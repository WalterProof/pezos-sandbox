<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Tags;

use PezosSandbox\Domain\Model\Tag\TagId;

final class Tag
{
    private TagId $tagId;
    private string $label;

    public function __construct(TagId $tagId, string $label)
    {
        $this->tagId = $tagId;
        $this->label = $label;
    }

    public function tagId(): string
    {
        return $this->tagId->asString();
    }

    public function label(): string
    {
        return $this->label;
    }
}
