<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Tokens;

final class TokenTag
{
    private string $tagId;
    private string $label;

    public function __construct(string $tagId, string $label)
    {
        $this->tagId = $tagId;
        $this->label = $label;
    }

    public function tagId(): string
    {
        return $this->tagId;
    }

    public function label(): string
    {
        return $this->label;
    }
}
