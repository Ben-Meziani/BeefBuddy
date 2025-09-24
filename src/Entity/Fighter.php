<?php

namespace App\Entity;

use App\Repository\FighterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FighterRepository::class)]
class Fighter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nickname = null;

    #[ORM\Column(length: 255)]
    private ?string $weightClass = null;

    #[ORM\Column]
    private ?int $wins = null;

    #[ORM\Column]
    private ?int $losses = null;

    #[ORM\Column]
    private ?int $draws = null;

    #[ORM\Column(nullable: true)]
    private ?int $noContest = null;

    #[ORM\Column(length: 255)]
    private ?string $country = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $birthday = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'fighters')]
    private Collection $users;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'fighter')]
    private Collection $reservations;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $height = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $weight = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reach = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Style = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $placeOfBirth = null;

    #[ORM\Column]
    private ?int $pricePerTraining = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->reservations = new ArrayCollection();
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

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): static
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getWeightClass(): ?string
    {
        return $this->weightClass;
    }

    public function setWeightClass(string $weightClass): static
    {
        $this->weightClass = $weightClass;

        return $this;
    }

    public function getWins(): ?int
    {
        return $this->wins;
    }

    public function setWins(int $wins): static
    {
        $this->wins = $wins;

        return $this;
    }

    public function getLosses(): ?int
    {
        return $this->losses;
    }

    public function setLosses(int $losses): static
    {
        $this->losses = $losses;

        return $this;
    }

    public function getDraws(): ?int
    {
        return $this->draws;
    }

    public function setDraws(int $draws): static
    {
        $this->draws = $draws;

        return $this;
    }

    public function getNoContest(): ?int
    {
        return $this->noContest;
    }

    public function setNoContest(?int $noContest): static
    {
        $this->noContest = $noContest;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTime $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getPlaceOfBirth(): ?string
    {
        return $this->placeOfBirth;
    }

    public function setPlaceOfBirth(?string $placeOfBirth): static
    {
        $this->placeOfBirth = $placeOfBirth;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setFighter($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getFighter() === $this) {
                $reservation->setFighter(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(?string $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getReach(): ?string
    {
        return $this->reach;
    }

    public function setReach(?string $reach): static
    {
        $this->reach = $reach;

        return $this;
    }

    public function getStance(): ?string
    {
        return $this->stance;
    }

    public function setStance(?string $stance): static
    {
        $this->stance = $stance;

        return $this;
    }

    public function getStyle(): ?string
    {
        return $this->Style;
    }

    public function setStyle(?string $Style): static
    {
        $this->Style = $Style;

        return $this;
    }

    public function getPricePerTraining(): ?int
    {
        return $this->pricePerTraining;
    }

    public function setPricePerTraining(int $pricePerTraining): static
    {
        $this->pricePerTraining = $pricePerTraining;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nickname' => $this->nickname,
            'weightClass' => $this->weightClass,
            'wins' => $this->wins,
            'losses' => $this->losses,
            'draws' => $this->draws,
            'noContest' => $this->noContest,
            'country' => $this->country,
            'birthday' => $this->birthday?->format('Y-m-d'),
            'placeOfBirth' => $this->placeOfBirth,
            'isActive' => $this->isActive,
            'description' => $this->description,
            'height' => $this->height,
            'weight' => $this->weight,
            'reach' => $this->reach,
            'stance' => $this->stance,
            'style' => $this->Style,
            'pricePerTraining' => $this->pricePerTraining,
        ];
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }
}
