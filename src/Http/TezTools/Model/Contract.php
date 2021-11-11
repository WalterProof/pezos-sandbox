<?php

declare(strict_types=1);

namespace App\Http\TezTools\Model;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class Contract
{
    public string $identifier;
    public string $symbol;
    public bool $shouldPreferSymbol = false;
    public string $tokenAddress;
    public int $tokenId;
    public ?string $name = null;
    public string $type;
    public string $address;
    public int $decimals;
    public string $totalSupply;
    public array $apps = [];
    public array $tags = [];
    public ?string $websiteLink = null;
    public ?string $telegramLink = null;
    public ?string $twitterLink = null;
    public ?string $discordLink = null;
    public ?string $thumbnailUri = null;

    public function setTags(string $tags)
    {
        $this->tags = explode(',', $tags);
    }

    public function setApps(array $apps)
    {
        $objectNormalizer = new ObjectNormalizer();

        foreach ($apps as $app) {
            $this->apps[] = $objectNormalizer->denormalize($app, App::class);
        }
    }
}
