<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['settings'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['settings'])]
    private ?bool $isWebsiteOpen = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['settings'])]
    private ?string $belongings = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['settings'])]
    private ?string $otherSharedRoom = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isWebsiteOpen(): ?bool
    {
        return $this->isWebsiteOpen;
    }

    public function setWebsiteOpen(bool $isWebsiteOpen): static
    {
        $this->isWebsiteOpen = $isWebsiteOpen;

        return $this;
    }

    public function getBelongings(): ?string
    {
        return $this->belongings;
    }

    public function setBelongings(?string $belongings): static
    {
        $this->belongings = $belongings;

        return $this;
    }

    public function getOtherSharedRoom(): ?string
    {
        return $this->otherSharedRoom;
    }

    public function setOtherSharedRoom(?string $otherSharedRoom): static
    {
        $this->otherSharedRoom = $otherSharedRoom;

        return $this;
    }
}
