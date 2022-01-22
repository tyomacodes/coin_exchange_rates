<?php

namespace App\Entity;

use App\Repository\PriceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PriceRepository::class)
 */
class Price
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=CoinPair::class, inversedBy="prices")
     */
    private $coin;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $avg_value;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ratio;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function getRoundValue(): ?float
    {
        return round($this->value, 4);
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getCoin(): ?CoinPair
    {
        return $this->coin;
    }

    public function setCoin(?CoinPair $coin): self
    {
        $this->coin = $coin;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAvgValue(): ?float
    {
        return $this->avg_value;
    }

    public function setAvgValue(?float $avg_value): self
    {
        $this->avg_value = $avg_value;

        return $this;
    }

    public function getRatio(): ?float
    {
        return $this->ratio;
    }

    public function setRatio(?float $ratio): self
    {
        $this->ratio = $ratio;

        return $this;
    }
}

