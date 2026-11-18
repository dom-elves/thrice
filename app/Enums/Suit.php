<?php

namespace App\Enums;

enum Suit: string
{
    case Hearts = 'hearts';
    case Diamonds = 'diamonds';
    case Clubs = 'clubs';
    case Spades = 'spades';

    // single letter shorthands for suits
    // used in generating card code, like '2h' for two of hearts
    public function shorthand(): string
    {
        return match ($this) {
            self::Hearts => 'h',
            self::Diamonds => 'd',
            self::Clubs => 'c',
            self::Spades => 's',
        };
    }
}
