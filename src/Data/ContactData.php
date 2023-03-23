<?php

namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class ContactData
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    public ?string $phone = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    public ?string $content = null;
}

