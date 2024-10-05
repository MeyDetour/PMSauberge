<?php

namespace App\Entity;

use App\Repository\BedRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: BedRepository::class)]
class Bed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['bed','rooms_and_bed'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['bed','rooms_and_bed'])]
    private ?bool $isDunkBed = null;

    #[ORM\Column]
    #[Groups(['bed','rooms_and_bed'])]
    private ?bool $isSittingApart = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['bed','rooms_and_bed'])]
    private ?string $state = null;
    //blocked, cleaned, inspected, notcleaned

    #[ORM\ManyToOne(inversedBy: 'beds')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['rooms'])]
    private ?Room $room = null;

    #[ORM\Column(unique: true)]
    #[Groups(['bed','rooms_and_bed'])]
    private ?int $number = null;

    #[ORM\ManyToOne(inversedBy: 'bedsCleaned')]
    #[Groups(['bed','rooms_and_bed'])]
    private ?User $cleanedBy = null;

    #[ORM\ManyToOne(inversedBy: 'bedsInspected')]
    #[Groups(['bed','rooms_and_bed'])]
    private ?User $inspectedBy = null;

    #[ORM\Column]
    private ?bool $isDoubleBed = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function isDunkBed(): ?bool
    {
        return $this->isDunkBed;
    }

    public function setDunkBed(bool $isDunkBed): static
    {
        $this->isDunkBed = $isDunkBed;

        return $this;
    }

    public function isSittingApart(): ?bool
    {
        return $this->isSittingApart;
    }

    public function setSittingApart(bool $isSittingApart): static
    {
        $this->isSittingApart = $isSittingApart;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getCleanedBy(): ?User
    {
        return $this->cleanedBy;
    }

    public function setCleanedBy(?User $cleanedBy): static
    {
        $this->cleanedBy = $cleanedBy;

        return $this;
    }

    public function getInspectedBy(): ?User
    {
        return $this->inspectedBy;
    }

    public function setInspectedBy(?User $inspectedBy): static
    {
        $this->inspectedBy = $inspectedBy;

        return $this;
    }

    public function isDoubleBed(): ?bool
    {
        return $this->isDoubleBed;
    }

    public function setDoubleBed(bool $isDoubleBed): static
    {
        $this->isDoubleBed = $isDoubleBed;

        return $this;
    }
}
