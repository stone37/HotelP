<?php

namespace App\Event;

use App\Entity\PasswordResetToken;
use App\Entity\User;

final class PasswordResetTokenCreatedEvent
{
    public function __construct(private PasswordResetToken $token)
    {
    }

    public function getUser(): User
    {
        return $this->token->getUser();
    }

    public function getToken(): PasswordResetToken
    {
        return $this->token;
    }
}
