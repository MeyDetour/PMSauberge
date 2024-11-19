<?php

namespace App\Entity;

use App\Service\DateService;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Serializer\Annotation as Serializer;
use App\Repository\BookingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['entireBooking','bed', 'rooms_and_bed'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['entireBooking','bed', 'rooms_and_bed'])]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['entireBooking','bed', 'rooms_and_bed'])]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column]
    #[Groups(['entireBooking'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY=>'U'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['entireBooking','bed', 'rooms_and_bed'])]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['entireBooking','bed', 'rooms_and_bed'])]
    private ?string $mail = null;

    #[ORM\Column]
    #[Groups(['entireBooking'])]
    private ?float $price = null;

    /**
     * @var Collection<int, Bed>
     */
    #[ORM\ManyToMany(targetEntity: Bed::class, inversedBy: 'bookings')]
    #[Groups(['entireBooking'])]
    private Collection $beds;

    #[ORM\Column]
    #[Groups(['entireBooking'])]
    private ?bool $isFinished = null;

    #[ORM\Column]
    #[Groups(['entireBooking'])]
    private ?bool $isPaid = null;

    #[ORM\Column(length: 255)]
    #[Groups(['entireBooking'])]
    private ?string $advencement = null;
    //refund,progress,done,waiting

    /**
     * @var Collection<int, Client>
     */
    #[ORM\ManyToMany(targetEntity: Client::class, mappedBy: 'booking',cascade:['persist'])]
    #[Groups(['entireBooking'])]
    private Collection $clients;

    #[ORM\Column]
    private ?bool $wantPrivateRoom = null;

    public function __construct()
    {    $this->beds = new ArrayCollection();
        $this->clients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return  $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return  $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

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

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

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
        }

        return $this;
    }

    public function removeBed(Bed $bed): static
    {
        $this->beds->removeElement($bed);

        return $this;
    }

    public function isFinished(): ?bool
    {
        return $this->isFinished;
    }

    public function setFinished(bool $isFinished): static
    {
        $this->isFinished = $isFinished;

        return $this;
    }

    public function isPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setPaid(bool $isPaid): static
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getAdvencement(): ?string
    {
        return $this->advencement;
    }

    public function setAdvencement(string $advencement): static
    {
        $this->advencement = $advencement;

        return $this;
    }

    /**
     * @return Collection<int, Client>
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): static
    {
        if (!$this->clients->contains($client)) {
            $this->clients->add($client);
            $client->addBooking($this);
        }

        return $this;
    }

    public function removeClient(Client $client): static
    {
        if ($this->clients->removeElement($client)) {
            $client->removeBooking($this);
        }

        return $this;
    }

    public function getWantPrivateRoom(): ?string
    {
        return $this->wantPrivateRoom;
    }

    public function setWantPrivateRoom(string $wantPrivateRoom): static
    {
        $this->wantPrivateRoom = $wantPrivateRoom;

        return $this;
    }
}
