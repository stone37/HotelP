<?php

namespace App\Entity;

use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
class Equipment
{
    use TimestampableTrait;
    use PositionTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: true)]
    private ?string $name = null;

    #[Gedmo\Slug(fields: ['name'], unique: true)]
    #[ORM\Column(length: 100)]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'equipment', targetEntity: EquipmentValue::class, cascade: ['ALL'], orphanRemoval: true)]
    private Collection $values;

    #[Pure] public function __construct()
    {
        $this->values = new ArrayCollection();
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

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

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

    public function getValues(): Collection
    {
        return $this->values;
    }

    public function addValue(EquipmentValue $value): self
    {
        if (!$this->values->contains($value)) {
            $this->values[] = $value;
            $value->setEquipment($this);
        }

        return $this;
    }

    public function removeValue(EquipmentValue $value): self
    {
        if ($this->values->removeElement($value)) {
            if ($value->getEquipment() === $this) {
                $value->setEquipment(null);
            }
        }

        return $this;
    }

    #[Pure] public function __toString(): string
    {
        return (string) $this->getName();
    }
}
