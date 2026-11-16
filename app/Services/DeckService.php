<?php

namespace App\Services;

use App\Models\Game;
use Illuminate\Support\Facades\Redis;
use App\Enums\Rank;
use App\Enums\Suit;
use App\ValueObjects\Card;
use Illuminate\Support\Collection;

class DeckService
{
    public function __construct() {

    }

    /** @return Collection<int, Card> */
    public function build(): Collection
    {
        $deck = collect(Rank::cases())
            ->crossJoin(Suit::cases())
            ->map(fn ($pair) => new Card(...$pair));
        
        return $deck;
    }

    public function deal(Game $game): void
    {
        $seats = Redis::hgetall("game_seats:{$game->code}");

        $deck = $this->build();
        dd($deck->shuffle());
    }
}