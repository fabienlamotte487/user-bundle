<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ForgotPasswordDTO
{
    #[Assert\NotBlank(message: "L'email est requis.")]
    #[Assert\Email(message: "L'email n'est pas valide.")]
    public ?string $email = null;
}
