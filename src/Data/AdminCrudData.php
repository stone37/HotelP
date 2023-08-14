<?php

namespace App\Data;

use App\Entity\User;
use App\Form\RegistrationAdminType;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AdminCrudData implements CrudDataInterface
{
    public ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 180)]
    public ?string $firstname = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 180)]
    public ?string $lastname = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 180)]
    #[Assert\Email(message: 'L\'adresse e-mail est invalide.')]
    public ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 180)]
    public ?string $phone = null;

    #[Assert\NotBlank]
    public array $roles = ['ROLE_ADMIN'];

    public ?string $plainPassword = null;

    public ?bool $isVerified = true;

    public ?User $entity;

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    #[Pure] public static function makeFromAdmin(User $admin, UserPasswordHasherInterface $passwordHasher): self
    {
        $data = new self($passwordHasher);
        $data->id = $admin->getId();
        $data->firstname = $admin->getFirstname();
        $data->lastname = $admin->getLastname();
        $data->email = $admin->getEmail();
        $data->phone = $admin->getPhone();
        $data->roles = $admin->getRoles();
        $data->isVerified = $admin->isVerified();
        $data->entity = $admin;

        return $data;
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return RegistrationAdminType::class;
    }

    public function hydrate(): void
    {
        $this->entity
            ->setFirstname($this->firstname)
            ->setLastname($this->lastname)
            ->setEmail($this->email)
            ->setPhone($this->phone)
            ->setRoles($this->roles)
            ->setIsVerified($this->isVerified)
            ->setPassword($this->plainPassword ? $this->passwordHasher->hashPassword($this->entity, $this->plainPassword) : '');
    }
}

