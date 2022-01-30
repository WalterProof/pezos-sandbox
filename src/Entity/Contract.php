<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ContractRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContractRepository::class)]
class Contract
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $symbol;

    #[ORM\Column(type: 'string', length: 255)]
    private $identifier;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $type;

    #[ORM\Column(type: 'integer')]
    private $decimals;

    #[ORM\Column(type: 'string')]
    private $totalSupply;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $thumbnailUri;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $websiteLink;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $telegramLink;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $twitterLink;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $discordLink;

    #[ORM\Column(type: 'boolean', options: ['default' => '0'])]
    private $shouldPreferSymbol;

    #[ORM\Column(type: 'json', nullable: true)]
    private $apps = [];

    #[ORM\Column(type: 'array', nullable: true)]
    private $tags = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDecimals(): ?int
    {
        return $this->decimals;
    }

    public function setDecimals(int $decimals): self
    {
        $this->decimals = $decimals;

        return $this;
    }

    public function getTotalSupply(): ?string
    {
        return $this->totalSupply;
    }

    public function setTotalSupply(string $totalSupply): self
    {
        $this->totalSupply = $totalSupply;

        return $this;
    }

    public function getThumbnailUri(): ?string
    {
        return $this->thumbnailUri;
    }

    public function setThumbnailUri(?string $thumbnailUri): self
    {
        $this->thumbnailUri = $thumbnailUri;

        return $this;
    }

    public function getWebsiteLink(): ?string
    {
        return $this->websiteLink;
    }

    public function setWebsiteLink(?string $websiteLink): self
    {
        $this->websiteLink = $websiteLink;

        return $this;
    }

    public function getTelegramLink(): ?string
    {
        return $this->telegramLink;
    }

    public function setTelegramLink(?string $telegramLink): self
    {
        $this->telegramLink = $telegramLink;

        return $this;
    }

    public function getTwitterLink(): ?string
    {
        return $this->twitterLink;
    }

    public function setTwitterLink(?string $twitterLink): self
    {
        $this->twitterLink = $twitterLink;

        return $this;
    }

    public function getDiscordLink(): ?string
    {
        return $this->discordLink;
    }

    public function setDiscordLink(?string $discordLink): self
    {
        $this->discordLink = $discordLink;

        return $this;
    }

    public function getShouldPreferSymbol(): ?bool
    {
        return $this->shouldPreferSymbol;
    }

    public function setShouldPreferSymbol(bool $shouldPreferSymbol): self
    {
        $this->shouldPreferSymbol = $shouldPreferSymbol;

        return $this;
    }

    public function getApps(): ?array
    {
        return $this->apps;
    }

    public function setApps(?array $apps): self
    {
        $this->apps = $apps;

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }
}
