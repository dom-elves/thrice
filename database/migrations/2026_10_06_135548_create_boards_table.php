<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('boards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->references('id')->on('games');
            // todo: add hands table ref later
            $table->json('bottom_flop');
            $table->string('bottom_turn');
            $table->string('bottom_river');
            $table->json('middle_flop');
            $table->string('middle_turn');
            $table->string('middle_river');
            $table->json('top_flop');
            $table->string('top_turn');
            $table->string('top_river');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boards');
    }
};
