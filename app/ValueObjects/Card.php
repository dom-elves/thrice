<?php

namespace App\ValueObjects;

use App\Enums\Rank;
use App\Enums\Suit;

readonly class Card
{
    public string $shorthand;

    public function __construct(
        public Rank $rank,
        public Suit $suit,
    ) {
        $this->shorthand = $rank->shorthand() . $suit->shorthand();
    }

    public function toArray(): array
    {
        return [
            'rank' => $this->rank->value,
            'suit' => $this->suit->value,
            'shorthand' => $this->shorthand,
        ];
    }
}