<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if not exists
        User::firstOrCreate(
            ['email' => 'admin@hamzaka.me'],
            [
                'name' => 'Hamza',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        
        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@hamzaka.me');
        $this->command->info('Password: password');
    }
}
