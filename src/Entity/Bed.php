<?php

namespace App\Entity;

use App\Repository\BedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ORM\Entity(repositoryClass: BedRepository::class)]
class Bed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['bed', 'rooms_and_bed', 'entireBooking','rooms'])]
    private ?int $id = null;


    #[ORM\Column]
    #[Groups(['bed', 'rooms_and_bed'])]
    #[SerializedName('sittingApart')]
    private ?bool $isSittingApart = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['bed', 'rooms_and_bed', 'entireBooking'])]
    private ?string $state = null;
    //blocked, cleaned, inspected, notcleaned, deleted

    #[ORM\ManyToOne(inversedBy: 'beds')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['entireBooking'])]
    private ?Room $room = null;

    #[ORM\Column(unique: true)]
    #[Groups(['bed', 'rooms_and_bed', 'entireBooking','rooms'])]
    private ?string $number = null;

    #[ORM\ManyToOne(inversedBy: 'bedsCleaned')]
    #[Groups(['bed'])]
    private ?User $cleanedBy = null;

    #[ORM\ManyToOne(inversedBy: 'bedsInspected')]
    #[Groups(['bed'])]
    private ?User $inspectedBy = null;

    #[ORM\Column]
    #[Groups(['bed', 'rooms_and_bed'])]
    #[SerializedName('doubleBed')]
    private ?bool $isDoubleBed = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['bed', 'rooms_and_bed'])]
    private ?string $bedShape = null;
// topBed,bottomBed,singleBed
    #[ORM\Column]
    #[Groups(['bed', 'rooms_and_bed'])]
    private ?bool $hasLamp = null;

    #[ORM\Column]
    #[Groups(['bed', 'rooms_and_bed'])]
    private ?bool $hasLittleStorage = null;

    #[ORM\Column]
    #[Groups(['bed', 'rooms_and_bed'])]
    private ?bool $hasShelf = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\ManyToMany(targetEntity: Booking::class, mappedBy: 'beds')]
    #[ORM\OrderBy(["startDate"=>"ASC"])]
    private Collection $bookings;

    #[ORM\Column]
    #[Groups(['rooms','bed'])]
    #[SerializedName('occupied')]
    private ?bool $isOccupied = null;

    #[ORM\ManyToOne(inversedBy: 'currentBookingInTheseBeds')]
    #[Groups(['bed','rooms_and_bed'])]
    private ?Booking $currentBooking = null;

    #[ORM\Column(nullable: true)]
    #[SerializedName('reservable')]
    #[Groups(['bed', 'rooms_and_bed','rooms'])]
    private ?bool $isReservable = null;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    //topBed - bottomBed - singleBed

    public function getId(): ?int
    {
        return $this->id;
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

    #[Groups(['bed'])]
    public function getRoomId(): ?int
    {
        if ($this->room){
            return $this->room->getId();
        }
       return 0;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
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

    public function getBedShape(): ?string
    {
        return $this->bedShape;
    }


    public function getCurrentBooking()
    {
       return $this->currentBooking;
    }

    public function setBedShape(?string $bedShape): static
    {
        $this->bedShape = $bedShape;
        return $this;
    }

    public function hasLamp(): ?bool
    {
        if (!$this->hasLamp){
            return false;
        }
        return $this->hasLamp;
    }

    public function setHasLamp(bool $hasLamp): static
    {
        $this->hasLamp = $hasLamp;

        return $this;
    }

    public function hasLittleStorage(): ?bool
    {
        if (!$this->hasLittleStorage){
            return false;
        }
        return $this->hasLittleStorage;
    }

    public function setHasLittleStorage(bool $hasLittleStorage): static
    {
        $this->hasLittleStorage = $hasLittleStorage;

        return $this;
    }

    public function hasShelf(): ?bool
    {   if (!$this->hasShelf){
        return false;
    }
        return $this->hasShelf;
    }

    public function setHasShelf(bool $hasShelf): static
    {
        $this->hasShelf = $hasShelf;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->addBed($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            $booking->removeBed($this);
        }

        return $this;
    }

    public function isOccupied(): ?bool
    {
        if (!$this->isOccupied){
            return false;
        }
        return $this->isOccupied;
    }

    public function setOccupied(bool $isOccupied): static
    {

        $this->isOccupied = $isOccupied;

        return $this;
    }

    public function setCurrentBooking(?Booking $currentBooking): static
    {
        $this->currentBooking = $currentBooking;

        return $this;
    }

    public function isReservable(): ?bool
    {
        if (!$this->isReservable){
            return false;
        }
        return $this->isReservable;
    }

    public function setReservable(?bool $isReservable): static
    {
        $this->isReservable = $isReservable;

        return $this;
    }
}
