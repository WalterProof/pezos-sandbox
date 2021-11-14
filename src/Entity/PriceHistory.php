<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PriceHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PriceHistoryRepository::class)
 */
class PriceHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @ORM\Column(type="decimal", precision=27, scale=18)
     */
    private $price;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=6)
     */
    private $tezpool;

    /**
     * @ORM\Column(type="decimal", precision=32, scale=18)
     */
    private $tokenpool;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getTezpool(): ?string
    {
        return $this->tezpool;
    }

    public function setTezpool(string $tezpool): self
    {
        $this->tezpool = $tezpool;

        return $this;
    }

    public function getTokenpool(): ?string
    {
        return $this->tokenpool;
    }

    public function setTokenpool(string $tokenpool): self
    {
        $this->tokenpool = $tokenpool;

        return $this;
    }
}
