<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Tezos\Storage;

use PezosSandbox\Infrastructure\Tezos\Contract;

interface GetStorage
{
    public function getStorage(Contract $contract): Storage;
}
