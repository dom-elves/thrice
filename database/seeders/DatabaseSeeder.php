<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Dom Elves',
            'email' => 'dom@example.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Dom2 Elves2',
            'email' => 'dom2@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}
