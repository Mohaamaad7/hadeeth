<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'mohaamaad7@gmail.com'],
            [
                'name' => 'Admin',
                'email' => 'mohaamaad7@gmail.com',
                'password' => Hash::make('18101978/**/'),
                'email_verified_at' => now(),
            ]
        );
    }
}
