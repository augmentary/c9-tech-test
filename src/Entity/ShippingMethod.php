<?php

namespace App\Entity;

use App\Repository\ShippingMethodRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ShippingMethodRepository::class)]
class ShippingMethod
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $shipTimeUk = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $shipTimeEurope = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $shipTimeRestOfWorld = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getShipTimeUk(): ?int
    {
        return $this->shipTimeUk;
    }

    public function setShipTimeUk(?int $shipTimeUk): static
    {
        $this->shipTimeUk = $shipTimeUk;

        return $this;
    }

    public function getShipTimeEurope(): ?int
    {
        return $this->shipTimeEurope;
    }

    public function setShipTimeEurope(?int $shipTimeEurope): static
    {
        $this->shipTimeEurope = $shipTimeEurope;

        return $this;
    }

    public function getShipTimeRestOfWorld(): ?int
    {
        return $this->shipTimeRestOfWorld;
    }

    public function setShipTimeRestOfWorld(?int $shipTimeRestOfWorld): static
    {
        $this->shipTimeRestOfWorld = $shipTimeRestOfWorld;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
