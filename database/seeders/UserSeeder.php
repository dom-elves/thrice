<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create 6 users
        $names = ['dom', 'harry', 'ty', 'bag', 'lewis', 'remi'];

        foreach ($names as $name) {
            User::factory()->create([
                'name' => $name,
                'email' => $name.'@example.com',
            ]);
        }
    }
}
