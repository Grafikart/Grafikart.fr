<?php

namespace App\Domain\Auth\Data;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordResetRequestData
{

    /**
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    public string $email = '';

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): PasswordResetRequestData
    {
        $this->email = $email;
        return $this;
    }



}
