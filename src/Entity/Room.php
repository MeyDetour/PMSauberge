<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'There is already a room with this name.')]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['rooms','rooms_and_bed'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT,unique: true)]
    #[Groups(['rooms','rooms_and_bed'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['rooms','rooms_and_bed'])]
    private ?bool $hasPrivateShowerroom = null;


    #[ORM\Column]
    #[Groups(['rooms','rooms_and_bed'])]
    private ?bool $hasLocker = null;

    #[ORM\Column]
    #[Groups(['rooms','rooms_and_bed'])]
    private ?bool $isPrivate = null;

    /**
     * @var Collection<int, Bed>
     */
    #[ORM\OneToMany(targetEntity: Bed::class, mappedBy: 'room', orphanRemoval: true)]
    #[Groups(['rooms_and_bed'])]
    private Collection $beds;

    public function __construct()
    {
        $this->beds = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function hasPrivateShowerroom(): ?bool
    {
        return $this->hasPrivateShowerroom;
    }

    public function setHasPrivateShowerroom(bool $hasPrivateShowerroom): static
    {
        $this->hasPrivateShowerroom = $hasPrivateShowerroom;

        return $this;
    }



    public function hasLocker(): ?bool
    {
        return $this->hasLocker;
    }

    public function setHasLocker(bool $hasLocker): static
    {
        $this->hasLocker = $hasLocker;

        return $this;
    }

    public function isPrivate(): ?bool
    {
        return $this->isPrivate;
    }

    public function setPrivate(bool $isPrivate): static
    {
        $this->isPrivate = $isPrivate;

        return $this;
    }

    /**
     * @return Collection<int, Bed>
     */
    public function getBeds(): Collection
    {
        return $this->beds;
    }

    public function addBed(Bed $bed): static
    {
        if (!$this->beds->contains($bed)) {
            $this->beds->add($bed);
            $bed->setRoom($this);
        }

        return $this;
    }

    public function removeBed(Bed $bed): static
    {
        if ($this->beds->removeElement($bed)) {
            // set the owning side to null (unless already changed)
            if ($bed->getRoom() === $this) {
                $bed->setRoom(null);
            }
        }

        return $this;
    }
}
