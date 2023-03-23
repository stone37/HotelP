<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    use TimestampableTrait;
    use PositionTrait;
    use EnabledTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2)]
    #[ORM\Column(nullable: true)]
    private ?string $name = null;

    #[Gedmo\Slug(fields: ['name'], unique: true)]
    #[ORM\Column(length: 100)]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    private ?string $smoker = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 0)]
    #[ORM\Column(nullable: true)]
    private ?int $roomNumber = null;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: true)]
    private ?int $price = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1)]
    #[ORM\Column(nullable: true)]
    private ?int $occupant = null;

    #[ORM\Column(nullable: true)]
    private ?int $area = null;

    #[ORM\Column(nullable: true)]
    private ?string $description  = null;

    #[ORM\Column(nullable: true)]
    private ?string $couchage = null;

    #[ORM\ManyToOne(targetEntity: Taxe::class)]
    private ?Taxe $taxe = null;

    #[ORM\OneToMany(mappedBy: 'room', targetEntity: Promotion::class, orphanRemoval: true)]
    private Collection $promotions;

    #[ORM\ManyToMany(targetEntity: Supplement::class, inversedBy: 'rooms')]
    private Collection $supplements;

    #[ORM\OneToMany(mappedBy: 'room', targetEntity: RoomGallery::class, cascade: ['ALL'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $galleries;

    #[ORM\OneToMany(mappedBy: 'room', targetEntity: Booking::class)]
    private Collection $bookings;

    #[ORM\ManyToMany(targetEntity: RoomEquipment::class, inversedBy: 'rooms')]
    private Collection $equipments;

    #[Pure] public function __construct()
    {
        $this->promotions = new ArrayCollection();
        $this->supplements = new ArrayCollection();
        $this->galleries = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->equipments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSmoker(): ?string
    {
        return $this->smoker;
    }

    public function setSmoker(?string $smoker): self
    {
        $this->smoker = $smoker;

        return $this;
    }

    public function getRoomNumber(): ?int
    {
        return $this->roomNumber;
    }

    public function setRoomNumber(?int $roomNumber): self
    {
        $this->roomNumber = $roomNumber;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getArea(): ?int
    {
        return $this->area;
    }

    public function setArea(?int $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getOccupant(): ?int
    {
        return $this->occupant;
    }

    public function setOccupant(?int $occupant): self
    {
        $this->occupant = $occupant;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCouchage(): ?string
    {
        return $this->couchage;
    }

    public function setCouchage(?string $couchage): self
    {
        $this->couchage = $couchage;

        return $this;
    }

    public function getPromotions(): ?Collection
    {
        return $this->promotions;
    }

    public function addPromotion(?Promotion $promotion): self
    {
        if (!$this->promotions->contains($promotion)) {
            $this->promotions[] = $promotion;
            $promotion->setRoom($this);
        }

        return $this;
    }

    public function removePromotion(?Promotion $promotion): self
    {
        if ($this->promotions->removeElement($promotion)) {
            // set the owning side to null (unless already changed)
            if ($promotion->getRoom() === $this) {
                $promotion->setRoom(null);
            }
        }

        return $this;
    }

    public function getTaxe(): ?Taxe
    {
        return $this->taxe;
    }

    public function setTaxe(?Taxe $taxe): self
    {
        $this->taxe = $taxe;

        return $this;
    }

    public function getSupplements(): ArrayCollection|Collection
    {
        return $this->supplements;
    }

    public function addSupplement(?Supplement $supplement): self
    {
        if (!$this->supplements->contains($supplement)) {
            $this->supplements[] = $supplement;
        }

        return $this;
    }

    public function removeSupplement(?Supplement $supplement): self
    {
        if ($this->supplements->removeElement($supplement)) {
            $supplement->removeRoom($this);
        }

        return $this;
    }

    public function getGalleries(): ?Collection
    {
        return $this->galleries;
    }

    public function addGallery(?RoomGallery $gallery): self
    {
        if (!$this->galleries->contains($gallery)) {
            $this->galleries[] = $gallery;
            $gallery->setRoom($this);
        }

        return $this;
    }

    public function removeGallery(?RoomGallery $gallery): self
    {
        if ($this->galleries->removeElement($gallery)) {
            // set the owning side to null (unless already changed)
            if ($gallery->getRoom() === $this) {
                $gallery->setRoom(null);
            }
        }

        return $this;
    }

    public function getBookings(): ?Collection
    {
        return $this->bookings;
    }

    public function addBooking(?Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setRoom($this);
        }

        return $this;
    }

    public function removeBooking(?Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getRoom() === $this) {
                $booking->setRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RoomEquipment>
     */
    public function getEquipments(): Collection
    {
        return $this->equipments;
    }

    public function addEquipment(RoomEquipment $equipment): self
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments[] = $equipment;
        }

        return $this;
    }

    public function removeEquipment(RoomEquipment $equipment): self
    {
        $this->equipments->removeElement($equipment);

        return $this;
    }

    public function getSupplementPrice(): int
    {
        $price = 0;

        /** @var Supplement $supplement */
        foreach ($this->supplements->toArray() as $supplement) {
            $price += (int) $supplement->getPrice();
        }

        return $price;
    }

    public function getTotalPrice(): ?int
    {
        return $this->getPrice() + $this->getSupplementPrice();
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
