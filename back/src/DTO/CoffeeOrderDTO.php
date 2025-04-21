<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CoffeeOrderDTO
{
    public const NAMES = [
        'espresso',
        'leppuccino',
        'cappuccino',
        'mocha',
        'espressino',
        'cafeViennois',
        'cafeLait',
        'macchiato'
    ];
    public const INTENSITIES = [
        'light',
        'medium',
        'strong'
    ];
    public const SIZES = [
        'small',
        'medium',
        'large'
    ];

    #[Assert\NotBlank(message: "Veuillez saisir un café.")]
    #[Assert\Choice(choices: self::NAMES, message: "Le nom doit être parmi les choix autorisés.")]
    public string $name;

    #[Assert\NotBlank(message:"Veuillez saisir une intensité.")]
    #[Assert\Choice(choices: self::INTENSITIES, message:"Les intensités disponibles light, medium et strong.")]
    public string $intensity;

    #[Assert\NotBlank(message:"Veuillez saisir une taille de café.")]
    #[Assert\Choice(choices: self::SIZES, message:"Les tailles disponibles sont small, medium et large.")]
    public string $size;
}