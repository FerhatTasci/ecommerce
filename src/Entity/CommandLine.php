<?php

namespace App\Entity;

use App\Repository\CommandLineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandLineRepository::class)]
class CommandLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $finalPriceUnit = null;

    #[ORM\Column]
    private ?int $vatRate = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    private ?SalesOrder $salesOrder = null;

    #[ORM\ManyToOne(inversedBy: 'commandLines')]
    private ?Product $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFinalPriceUnit(): ?int
    {
        return $this->finalPriceUnit;
    }

    public function setFinalPriceUnit(int $finalPriceUnit): static
    {
        $this->finalPriceUnit = $finalPriceUnit;

        return $this;
    }

    public function getVatRate(): ?int
    {
        return $this->vatRate;
    }

    public function setVatRate(int $vatRate): static
    {
        $this->vatRate = $vatRate;

        return $this;
    }

    public function getSalesOrder(): ?SalesOrder
    {
        return $this->salesOrder;
    }

    public function setSalesOrder(?SalesOrder $salesOrder): static
    {
        $this->salesOrder = $salesOrder;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }
}
