<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $validated = false;

    #[ORM\Column(nullable: true)]
    private ?string $number = null;

    #[ORM\Column(nullable: true)]
    private ?string $address = null;

    #[ORM\Column(nullable: true)]
    private ?int $amount = 0;

    #[ORM\Column(nullable: true)]
    private ?int $amountTotal = 0;

    #[ORM\Column(nullable: true)]
    private ?int $taxeAmount = null; 

    #[ORM\Column(nullable: true)]
    private ?int $discountAmount = null;

    #[ORM\OneToOne(inversedBy: 'commande', targetEntity: Payment::class, cascade: ['persist', 'remove'])]
    private ?Payment $payment = null;

    #[ORM\ManyToOne(targetEntity: Discount::class, inversedBy: 'commandes')]
    private ?Discount $discount = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'commandes')]
    private ?User $user = null;

    #[ORM\OneToOne(inversedBy: 'commande', targetEntity: Booking::class, cascade: ['persist', 'remove'])]
    private ?Booking $booking = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): self
    {
        $this->validated = $validated;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAmountTotal(): ?int
    {
        return $this->amountTotal;
    }

    public function setAmountTotal(?int $amountTotal): self
    {
        $this->amountTotal = $amountTotal;

        return $this;
    }

    public function getTaxeAmount(): ?int
    {
        return $this->taxeAmount;
    }

    public function setTaxeAmount(?int $taxeAmount): self
    {
        $this->taxeAmount = $taxeAmount;

        return $this;
    }

    public function getDiscountAmount(): ?int
    {
        return $this->discountAmount;
    }

    public function setDiscountAmount(?int $discountAmount): self
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getDiscount(): ?Discount
    {
        return $this->discount;
    }

    public function setDiscount(?Discount $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User|UserInterface|null $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): self
    {
        $this->booking = $booking;

        return $this;
    }
}
