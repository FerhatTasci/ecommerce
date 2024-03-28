<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $number = null;

    #[ORM\Column(length: 255)]
    private ?string $street = null;

    #[ORM\Column(length: 255)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $AdditionalAddress = null;

    #[ORM\OneToMany(mappedBy: 'address', targetEntity: UserAddress::class)]
    private Collection $userAddresses;

    #[ORM\OneToMany(mappedBy: 'address', targetEntity: OrderAddress::class)]
    private Collection $orderAddresses;

    public function __construct()
    {
        $this->userAddresses = new ArrayCollection();
        $this->orderAddresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getAdditionalAddress(): ?string
    {
        return $this->AdditionalAddress;
    }

    public function setAdditionalAddress(?string $AdditionalAddress): static
    {
        $this->AdditionalAddress = $AdditionalAddress;

        return $this;
    }

    /**
     * @return Collection<int, UserAddress>
     */
    public function getUserAddresses(): Collection
    {
        return $this->userAddresses;
    }

    public function addUserAddress(UserAddress $userAddress): static
    {
        if (!$this->userAddresses->contains($userAddress)) {
            $this->userAddresses->add($userAddress);
            $userAddress->setAddress($this);
        }

        return $this;
    }

    public function removeUserAddress(UserAddress $userAddress): static
    {
        if ($this->userAddresses->removeElement($userAddress)) {
            // set the owning side to null (unless already changed)
            if ($userAddress->getAddress() === $this) {
                $userAddress->setAddress(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderAddress>
     */
    public function getOrderAddresses(): Collection
    {
        return $this->orderAddresses;
    }

    public function addOrderAddress(OrderAddress $orderAddress): static
    {
        if (!$this->orderAddresses->contains($orderAddress)) {
            $this->orderAddresses->add($orderAddress);
            $orderAddress->setAddress($this);
        }

        return $this;
    }

    public function removeOrderAddress(OrderAddress $orderAddress): static
    {
        if ($this->orderAddresses->removeElement($orderAddress)) {
            // set the owning side to null (unless already changed)
            if ($orderAddress->getAddress() === $this) {
                $orderAddress->setAddress(null);
            }
        }

        return $this;
    }
}
