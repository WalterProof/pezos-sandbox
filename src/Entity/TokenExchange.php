<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TokenExchangeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=TokenExchangeRepository::class)
 */
class TokenExchange
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=36)
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity=Exchange::class, inversedBy="tokenExchanges")
     * @ORM\JoinColumn(nullable=false)
     */
    private $exchange;

    /**
     * @ORM\ManyToOne(targetEntity=Token::class, inversedBy="tokenExchanges")
     * @ORM\JoinColumn(nullable=false)
     */
    private $token;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): Uuid
    {
        return $this->id;
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

    public function getExchange(): ?Exchange
    {
        return $this->exchange;
    }

    public function setExchange(?Exchange $exchange): self
    {
        $this->exchange = $exchange;

        return $this;
    }

    public function getToken(): ?Token
    {
        return $this->token;
    }

    public function setToken(?Token $token): self
    {
        $this->token = $token;

        return $this;
    }
}
