<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

final class AddTag
{
    private string $label;

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    public function label(): string
    {
        return $this->label;
    }
}
