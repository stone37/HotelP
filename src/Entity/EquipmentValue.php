<?php

namespace App\Entity;

use App\Repository\EquipmentValueRepository;
use BadMethodCallException;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EquipmentValueRepository::class)]
class EquipmentValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $value = null;

    #[ORM\ManyToOne(targetEntity: Equipment::class, inversedBy: 'values')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Equipment $equipment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(?Equipment $equipment): self
    {
        $this->equipment = $equipment;

        return $this;
    }

    public function getEquipmentCode(): ?string
    {
        if (null === $this->equipment) {
            throw new BadMethodCallException(
                'The equipment have not been created yet so you cannot access proxy methods.'
            );
        }

        return $this->equipment->getSlug();
    }

    public function getName(): ?string
    {
        if (null === $this->equipment) {
            throw new BadMethodCallException('The option have not been created yet so you cannot access proxy methods.');
        }

        return $this->equipment->getName();
    }

    #[Pure] public function __toString(): string
    {
        return (string) $this->getValue();
    }
}
