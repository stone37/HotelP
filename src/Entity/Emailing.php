<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\EmailingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailingRepository::class)]
class Emailing
{
    public const GROUP_PARTICULIER = 'particulier';
    public const GROUP_USER = 'user';
    public const GROUP_NEWSLETTER = 'newsletter';

    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?string $destinataire = null;

    #[ORM\Column(nullable: true)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $groupe = self::GROUP_PARTICULIER;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDestinataire(): ?string
    {
        return $this->destinataire;
    }

    public function setDestinataire(?string $destinataire): self
    {
        $this->destinataire = $destinataire;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getGroupe(): ?string
    {
        return $this->groupe;
    }

    public function setGroupe(string $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }
}
