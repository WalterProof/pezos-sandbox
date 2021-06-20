<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Tag;

use PezosSandbox\Domain\Model\Common\AbstractUserFacingError;

final class CouldNotFindTag extends AbstractUserFacingError
{
    public static function withId(TagId $tagId): self
    {
        return new self('id.does_not_exist', [
            '{tagId}' => $tagId->asString(),
        ]);
    }
}
