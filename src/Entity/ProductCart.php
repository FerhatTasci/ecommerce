<?php

namespace App\Entity;

use App\Repository\ProductCartRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductCartRepository::class)]
class ProductCart
{
    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\ManyToOne(inversedBy: 'productCarts')]
    private ?Product $product = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\ManyToOne(inversedBy: 'productCarts')]
    private ?Cart $cart = null;

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

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

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): static
    {
        $this->cart = $cart;

        return $this;
    }
}
