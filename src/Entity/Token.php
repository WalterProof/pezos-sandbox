<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TokenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=TokenRepository::class)
 */
class Token
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tokenId;

    /**
     * @ORM\Column(type="string", length=36)
     */
    private $address;

    /**
     * @ORM\Column(type="json")
     */
    private $metadata = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @ORM\OneToMany(targetEntity=TokenExchange::class, mappedBy="token")
     */
    private $tokenExchanges;

    public function __construct()
    {
        $this->id             = Uuid::v4();
        $this->tokenExchanges = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTokenId(): ?int
    {
        return $this->tokenId;
    }

    public function setTokenId(?int $tokenId): self
    {
        $this->tokenId = $tokenId;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection|TokenExchange[]
     */
    public function getTokenExchanges(): Collection
    {
        return $this->tokenExchanges;
    }

    public function addTokenExchange(TokenExchange $tokenExchange): self
    {
        if (!$this->tokenExchanges->contains($tokenExchange)) {
            $this->tokenExchanges[] = $tokenExchange;
            $tokenExchange->setRel($this);
        }

        return $this;
    }

    public function removeTokenExchange(TokenExchange $tokenExchange): self
    {
        if ($this->tokenExchanges->removeElement($tokenExchange)) {
            // set the owning side to null (unless already changed)
            if ($tokenExchange->getRel() === $this) {
                $tokenExchange->setRel(null);
            }
        }

        return $this;
    }
}
