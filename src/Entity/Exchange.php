<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\ExchangeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=ExchangeRepository::class)
 */
class Exchange
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $homepage;

    /**
     * @ORM\OneToMany(targetEntity=TokenExchange::class, mappedBy="relation")
     */
    private $tokenExchanges;

    public function __construct()
    {
        $this->id = Uuid::v4(); 
        $this->tokenExchanges = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getHomepage(): ?string
    {
        return $this->homepage;
    }

    public function setHomepage(string $homepage): self
    {
        $this->homepage = $homepage;

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
            $tokenExchange->setRelation($this);
        }

        return $this;
    }

    public function removeTokenExchange(TokenExchange $tokenExchange): self
    {
        if ($this->tokenExchanges->removeElement($tokenExchange)) {
            // set the owning side to null (unless already changed)
            if ($tokenExchange->getRelation() === $this) {
                $tokenExchange->setRelation(null);
            }
        }

        return $this;
    }
}
