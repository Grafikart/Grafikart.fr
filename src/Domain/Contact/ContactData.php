<?php

namespace App\Domain\Contact;

use Symfony\Component\Validator\Constraints as Assert;

class ContactData
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="3")
     */
    public string $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public string $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="10")
     */
    public string $content;
}
