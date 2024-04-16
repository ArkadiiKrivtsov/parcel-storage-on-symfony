<?php

namespace App\Entity;

use App\Repository\ParcelEntityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParcelEntityRepository::class)]
class ParcelEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $estimatedCost = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sender $sender = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recipient $receiver = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dimensions $dimensions = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstimatedCost(): ?int
    {
        return $this->estimatedCost;
    }

    public function setEstimatedCost(int $estimatedCost): static
    {
        $this->estimatedCost = $estimatedCost;

        return $this;
    }

    public function getSender(): ?Sender
    {
        return $this->sender;
    }

    public function setSender(?Sender $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?Recipient
    {
        return $this->receiver;
    }

    public function setReceiver(?Recipient $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getDimensions(): ?Dimensions
    {
        return $this->dimensions;
    }

    public function setDimensions(?Dimensions $dimensions): static
    {
        $this->dimensions = $dimensions;

        return $this;
    }

    public function setId($id): static
    {
        $this->id = $id;

        return $this;
    }
}
