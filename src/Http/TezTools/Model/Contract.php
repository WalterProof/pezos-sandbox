<?php

declare(strict_types=1);

namespace App\Http\TezTools\Model;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class Contract
{
    public string $identifier;
    public string $symbol;
    public string $tokenAddress;
    public int $tokenId;
    public string $name;
    public string $type;
    public string $address;
    public string $thumbnailUri;
    public int $decimals;
    public string $tags;
    public array $apps;

    public function setApps(array $apps)
    {
        $objectNormalizer = new ObjectNormalizer();

        foreach ($apps as $app) {
            $this->apps[] = $objectNormalizer->denormalize($app, App::class);
        }
    }
}
