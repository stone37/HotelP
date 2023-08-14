<?php

namespace App\Data;

use App\Form\SettingsType;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class SettingsCrudData extends AutomaticCrudData
{
    #[Assert\NotBlank]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    public ?string $phone = null;

    public ?string $description = null;

    public ?string $fax = null;

    public ?string $address = null;

    public ?string $country = null;

    public ?string $city = null;

    public ?string $facebookAddress = null;

    public ?string $twitterAddress = null;

    public ?string $instagramAddress = null;

    public ?string $youtubeAddress = null;

    public ?string $linkedinAddress = null;

    public ?UploadedFile $file = null;

    public ?DateTimeInterface $checkinTime = null;

    public ?DateTimeInterface $checkoutTime = null;

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return SettingsType::class;
    }

    public function hydrate(): void
    {
        $this->entity
            ->setName($this->name)
            ->setEmail($this->email)
            ->setPhone($this->phone)
            ->setDescription($this->description)
            ->setFax($this->fax)
            ->setAddress($this->address)
            ->setCountry($this->country)
            ->setCity($this->city)
            ->setFacebookAddress($this->facebookAddress)
            ->setTwitterAddress($this->twitterAddress)
            ->setInstagramAddress($this->instagramAddress)
            ->setYoutubeAddress($this->youtubeAddress)
            ->setLinkedinAddress($this->linkedinAddress)
            ->setCheckinTime($this->checkinTime)
            ->setCheckoutTime($this->checkoutTime)
            ->setFile($this->file);
    }
}