<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\TaxeRepository;
use Doctrine\DBAL\Types\Types;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaxeRepository::class)]
class Taxe
{
    use EnabledTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: true)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $value = 0.0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(?float $value): self
    {
        $this->value = $value;

        return $this;
    }

    #[Pure] public function getLabel(): ?string
    {
        return sprintf('%s (%s%%)', $this->name, $this->getValue());
    }
}
