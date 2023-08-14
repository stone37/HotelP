<?php

namespace App\Exception;

use App\Entity\EmailVerification;
use Exception;
use JetBrains\PhpStorm\Pure;

class TooManyEmailChangeException extends Exception
{
    public EmailVerification $emailVerification;

    #[Pure] public function __construct(EmailVerification $emailVerification)
    {
        parent::__construct();
        $this->emailVerification = $emailVerification;
    }
}
