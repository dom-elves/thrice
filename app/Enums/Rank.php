<?php

namespace App\Enums;

enum Rank: string
{
    case Two = '2';
    case Three = '3';
    case Four = '4';
    case Five = '5';
    case Six = '6';
    case Seven = '7';
    case Eight = '8';
    case Nine = '9';
    case Ten = '10';
    case Jack = 'J';
    case Queen = 'Q';
    case King = 'K';
    case Ace = 'A';

    public function name(): string
    {
        return match ($this) {
            self::Two => 'two',
            self::Three => 'three',
            self::Four => 'four',
            self::Five => 'five',
            self::Six => 'six',
            self::Seven => 'seven',
            self::Eight => 'eight',
            self::Nine => 'nine',
            self::Ten => 'ten',
            self::Jack => 'jack',
            self::Queen => 'queen',
            self::King => 'king',
            self::Ace => 'ace',
        };
    }
}
