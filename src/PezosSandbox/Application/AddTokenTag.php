<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Domain\Model\Tag\TagId;
use PezosSandbox\Domain\Model\Token\TokenId;

final class AddTokenTag
{
    private string $tokenId;
    private string $tagId;

    public function __construct(string $tokenId, string $tagId)
    {
        $this->tokenId = $tokenId;
        $this->tagId   = $tagId;
    }

    public function tokenId(): TokenId
    {
        return TokenId::fromString($this->tokenId);
    }

    public function tagId(): TagId
    {
        return TagId::fromString($this->tagId);
    }
}
