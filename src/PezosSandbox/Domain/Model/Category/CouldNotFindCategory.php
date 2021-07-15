<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\category;

use PezosSandbox\Domain\Model\Common\AbstractUserFacingError;

final class CouldNotFindcategory extends AbstractUserFacingError
{
    public static function withId(categoryId $categoryId): self
    {
        return new self('id.does_not_exist', [
            '{categoryId}' => $categoryId->asString(),
        ]);
    }
}
