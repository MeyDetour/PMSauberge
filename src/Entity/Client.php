<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['entireBooking','clients'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['entireBooking','clients'])]
    private ?string $firstName = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['entireBooking','clients'])]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['entireBooking','clients'])]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(type: Types::TEXT,nullable: True)]
    private ?string $gender = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\ManyToMany(targetEntity: Booking::class, inversedBy: 'clients')]
    #[Groups(['clients'])]
    private Collection $bookings;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'clients')]
    #[ORM\JoinColumn(nullable: true)]
    private ?self $invitedBy = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'invitedBy')]
    private Collection $clients;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'mainClient')]
    #[Groups(['clients'])]
    private Collection $bookingsAuthor;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->clients = new ArrayCollection();
        $this->bookingsAuthor = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

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
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        $this->bookings->removeElement($booking);

        return $this;
    }

    public function getInvitedBy(): ?self
    {
        return $this->invitedBy;
    }

    public function setInvitedBy(?self $invitedBy): static
    {
        $this->invitedBy = $invitedBy;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(self $client): static
    {
        if (!$this->clients->contains($client)) {
            $this->clients->add($client);
            $client->setInvitedBy($this);
        }

        return $this;
    }

    public function removeClient(self $client): static
    {
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getInvitedBy() === $this) {
                $client->setInvitedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookingsAuthor(): Collection
    {
        return $this->bookingsAuthor;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }
}
