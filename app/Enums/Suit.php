<?php

namespace App\Enums;

enum Suit: string
{
    case Hearts = 'h';
    case Diamonds = 'd';
    case Clubs = 'c';
    case Spades = 's';

    public function name(): string
    {
        return match ($this) {
            self::Hearts => 'hearts',
            self::Diamonds => 'diamonds',
            self::Clubs => 'clubs',
            self::Spades => 'spades',
        };
    }
}
