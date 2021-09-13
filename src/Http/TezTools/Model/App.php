<?php

declare(strict_types=1);

namespace App\Http\TezTools\Model;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class App
{
    public string $name;
    public string $type;
    public array $pools;

    public function setPools(array $pools)
    {
        $objectNormalizer = new ObjectNormalizer();

        foreach ($pools as $pool) {
            $this->pools[] = $objectNormalizer->denormalize($pool, Pool::class);
        }
    }
}
