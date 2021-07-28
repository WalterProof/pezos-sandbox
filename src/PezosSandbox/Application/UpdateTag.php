<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Domain\Model\Tag\TagId;

final class UpdateTag
{
    private string $tagId;
    private string $label;

    public function __construct(string $tagId, string $label)
    {
        $this->tagId = $tagId;
        $this->label = $label;
    }

    public function tagId(): TagId
    {
        return TagId::fromString($this->tagId);
    }

    public function label(): string
    {
        return $this->label;
    }
}
