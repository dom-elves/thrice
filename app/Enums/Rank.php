<?php

namespace App\Enums;

enum Rank: int
{
    case Ace = 1;
    case Two = 2;
    case Three = 3;
    case Four = 4;
    case Five = 5;
    case Six = 6;
    case Seven = 7;
    case Eight = 8;
    case Nine = 9;
    case Ten = 10;
    case Jack = 11;
    case Queen = 12;
    case King = 13;

    // single letter shorthands for suits
    // used in generating card code, like '2h' for two of hearts
    public function shorthand(): string
    {
        return match ($this) {
            self::Ace => 'A',
            self::Two => '2',
            self::Three => '3',
            self::Four => '4',
            self::Five => '5',
            self::Six => '6',
            self::Seven => '7',
            self::Eight => '8',
            self::Nine => '9',
            self::Ten => '10',
            self::Jack => 'J',
            self::Queen => 'Q',
            self::King => 'K',
        };
    }

    // todo: figure out high ace
}
