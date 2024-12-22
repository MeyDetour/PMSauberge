<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\PseudoTypes\List_;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'There is already a room with this name.')]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['rooms','rooms_and_bed','entireBooking'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT,unique: true)]
    #[Groups(['rooms','rooms_and_bed','entireBooking'])]
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

    #[ORM\Column]
    #[Groups(['rooms','rooms_and_bed'])]
    private ?bool $hasTable = null;

    #[ORM\Column]
    #[Groups(['rooms','rooms_and_bed'])]
    private ?bool $hasBalcony = null;

    #[ORM\Column]
    #[Groups(['rooms','rooms_and_bed'])]
    private ?bool $hasWashtub = null;

    #[ORM\Column]
    #[Groups(['rooms','rooms_and_bed'])]
    private ?bool $hasBin = null;

    #[ORM\Column]
    #[Groups(['rooms','rooms_and_bed'])]
    private ?bool $hasWardrobe = null;

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

    public function getBeds(): array
    {
        $beds = [];
        foreach ($this->beds as $bed){
            if ($bed->getState() !="deleted"){
                $beds[]=$bed;
            }
        }

        return $beds;
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

    public function hasTable(): ?bool
    {
        return $this->hasTable;
    }

    public function setHasTable(bool $hasTable): static
    {
        $this->hasTable = $hasTable;

        return $this;
    }

    public function hasBalcony(): ?bool
    {
        return $this->hasBalcony;
    }

    public function setHasBalcony(bool $hasBalcony): static
    {
        $this->hasBalcony = $hasBalcony;

        return $this;
    }

    public function hasWashtub(): ?bool
    {
        return $this->hasWashtub;
    }

    public function setHasWashtub(bool $hasWashtub): static
    {
        $this->hasWashtub = $hasWashtub;

        return $this;
    }

    public function hasBin(): ?bool
    {
        return $this->hasBin;
    }

    public function setHasBin(bool $hasBin): static
    {
        $this->hasBin = $hasBin;

        return $this;
    }

    public function hasWardrobe(): ?bool
    {
        return $this->hasWardrobe;
    }

    public function setHasWardrobe(bool $hasWardrobe): static
    {
        $this->hasWardrobe = $hasWardrobe;

        return $this;
    }
    public function getBedNumber(): ?bool
    {
        $count = 0;
        foreach ($this->beds as $bed){
            if ($bed->getState()!="deleted"){
                if($bed->isDoubleBed()){
                    $count += 2;
                }else{
                    $count++;
                }
            }

        }
        return $count;
    }

    #[Groups(['rooms','rooms_and_bed'])]
    public function getBedsNumber(){
return count( $this->beds);
    }
}
