<?php

namespace App\Entity;

use App\Repository\OrderAddressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderAddressRepository::class)]
class OrderAddress
{
    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\ManyToOne(inversedBy: 'orderAddresses')]
    private ?SalesOrder $salesOrder = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\ManyToOne(inversedBy: 'orderAddresses')]
    private ?Address $address = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;

        return $this;
    }
}
