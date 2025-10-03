<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordDTO
{
    #[Assert\NotBlank(message: "Le mot de passe est requis.")]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
        message: "Le mot de passe doit contenir au minimum 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial",
    )]
    public ?string $plainPassword = null;

    #[Assert\NotBlank(message: "Le token est requis.")]
    public ?string $token = null;
}