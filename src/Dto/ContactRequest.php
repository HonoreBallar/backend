<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ContactRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        public readonly string $name = '',

        #[Assert\NotBlank]
        #[Assert\Email]
        #[Assert\Length(max: 180)]
        public readonly string $email = '',

        #[Assert\NotBlank]
        #[Assert\Length(min: 10, max: 5000)]
        public readonly string $message = '',

        /** Honeypot: hidden in the form, only bots fill it */
        public readonly string $website = '',
    ) {
    }
}
