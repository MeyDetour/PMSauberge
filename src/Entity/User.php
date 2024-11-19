<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user','bed'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['user','bed'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['user'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['user'])]
    private ?string $firstName = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['user'])]
    private ?string $lastName = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['user'])]
    private ?string $website = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['user'])]
    private ?string $profession = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['user'])]
    private ?string $phoneNumber = null;

    /**
     * @var Collection<int, Bed>
     */
    #[ORM\OneToMany(targetEntity: Bed::class, mappedBy: 'cleanedBy')]
    private Collection $bedsCleaned;

    /**
     * @var Collection<int, Bed>
     */
    #[ORM\OneToMany(targetEntity: Bed::class, mappedBy: 'inspectedBy')]
    private Collection $bedsInspected;

    #[ORM\Column]
    #[Groups(['user'])]
    private ?bool $isActive = null;

    public function __construct()
    {
        $this->bedsCleaned = new ArrayCollection();
        $this->bedsInspected = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $CreatedAt): static
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;

        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(string $profession): static
    {
        $this->profession = $profession;

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

    /**
     * @return Collection<int, Bed>
     */
    public function getBedsCleaned(): Collection
    {
        return $this->bedsCleaned;
    }

    public function addBedsCleaned(Bed $bedsCleaned): static
    {
        if (!$this->bedsCleaned->contains($bedsCleaned)) {
            $this->bedsCleaned->add($bedsCleaned);
            $bedsCleaned->setCleanedBy($this);
        }

        return $this;
    }

    public function removeBedsCleaned(Bed $bedsCleaned): static
    {
        if ($this->bedsCleaned->removeElement($bedsCleaned)) {
            // set the owning side to null (unless already changed)
            if ($bedsCleaned->getCleanedBy() === $this) {
                $bedsCleaned->setCleanedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bed>
     */
    public function getBedsInspected(): Collection
    {
        return $this->bedsInspected;
    }

    public function addBedsInspected(Bed $bedsInspected): static
    {
        if (!$this->bedsInspected->contains($bedsInspected)) {
            $this->bedsInspected->add($bedsInspected);
            $bedsInspected->setInspectedBy($this);
        }

        return $this;
    }

    public function removeBedsInspected(Bed $bedsInspected): static
    {
        if ($this->bedsInspected->removeElement($bedsInspected)) {
            // set the owning side to null (unless already changed)
            if ($bedsInspected->getInspectedBy() === $this) {
                $bedsInspected->setInspectedBy(null);
            }
        }

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }
}
